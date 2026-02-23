<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show admin login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Admin login
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    // Use admin guard
    if (Auth::guard('admin')->attempt($credentials)) {
        $request->session()->regenerate();

        // ✅ Redirect directly to products.index
        return redirect()->route('products.index'); 
    }

    return back()->with('error', 'Invalid credentials.');
}


    // Admin logout
   public function logout(Request $request)
{
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
}


    // Show all users (for admin)
    public function index()
    {
        $users = User::where('role', 'user')->get(); // only normal users
       return view('user_mangment.all_user', compact('users'));

    }

    // Approve user & send OTP
  public function approve($id)
{
    $user = User::findOrFail($id);
    $user->status = 'approved';
    $user->otp = rand(100000, 999999); // 6 digit OTP
    $user->otp_verified_at = null;
    $user->save();

    // Send OTP email
    Mail::raw("Your OTP is: {$user->otp}. It expires in 24 hours.", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Your OTP for login');
    });

    return response()->json(['status' => true, 'message' => 'User approved & OTP sent']);
}

  public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|numeric',
    ]);

    $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('status', 'approved')
                ->first();

    if (!$user) {
        return response()->json(['status' => false, 'message' => 'Invalid OTP'], 422);
    }

    // OTP verified
    $user->update([
        'otp_verified_at' => now(),
        'otp' => null
    ]);

    return response()->json(['status' => true, 'message' => 'OTP verified successfully']);
}


}
