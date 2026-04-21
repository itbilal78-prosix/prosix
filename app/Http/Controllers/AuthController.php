<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
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

            // return redirect()->route('products.index');
            return redirect()->route('admin.dashboard');

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
        $users = User::where('role', 'user')->latest()->get();

        return view('user_mangment.all_user', compact('users'));
    }


    public function toggleStatus($id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->status == 'blocked') {
    $user->status = 'approved'; // unblock
} else {
    $user->status = 'blocked'; // block


            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        $user->save();

        return back()->with('success', 'User status updated.');
    }





public function loginAsUser($id)
{
    $user = User::findOrFail($id);

    if ($user->status !== 'approved') {
        return back()->with('error', 'User not approved');
    }

    $user->tokens()->delete();
    $token = $user->createToken('admin_impersonation')->plainTextToken;

    return redirect('/dashboard?token=' . $token . '&impersonate=1&tab=my-design');
}
}
