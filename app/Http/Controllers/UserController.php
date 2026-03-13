<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'pending',
            'otp' => $otp,
        ]);

        // Send OTP immediately
      try {
    $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
        ->setApiKey('api-key', env('BREVO_API_KEY'));

    $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
        new \GuzzleHttp\Client(), $config
    );

    $email = new \SendinBlue\Client\Model\SendSmtpEmail([
        'subject' => 'Your OTP Verification Code',
        'sender'  => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
        'to'      => [['email' => $user->email]],
        'htmlContent' => '<h2>Your OTP Code</h2><p style="font-size:32px;font-weight:bold;">'.$otp.'</p><p>This OTP will expire soon.</p>',
    ]);

    $apiInstance->sendTransacEmail($email);
} catch (\Exception $e) {
    \Log::error('OTP email failed: '.$e->getMessage());
}

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email',
            'email' => $user->email,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
            ], 422);
        }

        $user->update([
            'status' => 'approved',
            'otp_verified_at' => now(),
            'otp' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. You can login now.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

       if ($user->status == 'blocked') {
    Auth::logout();
    return response()->json([
        'status' => false,
        'message' => 'You are blocked. Please contact admin.',
    ], 403);
}

if ($user->status == 'pending') {
    Auth::logout();
    return response()->json([
        'status' => false,
        'message' => 'Please verify your OTP first.',
    ], 403);
}

        if ($user->status != 'approved') {
            Auth::logout();

            return response()->json([
                'status' => false,
                'message' => 'OTP verification required',
            ], 403);
        }

        // ✅ TOKEN CREATE KARO
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'token' => $token,
            'message' => 'Login successful',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    // Profile

    public function profile(Request $request)
    {
        $user = User::find($request->user()->id);

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->status == 'blocked') {
            $user->tokens()->delete();

            return response()->json([
                'status' => false,
                'message' => 'You are blocked.',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $user,
        ]);
    }

// ✅ Profile Update
public function updateProfile(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        'phone'    => 'nullable|string|max:20',
        'location' => 'nullable|string|max:255',
    ]);

    $user->update([
        'name'     => $request->name,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'location' => $request->location,
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Profile updated successfully.',
        'data'    => $user->fresh(),
    ]);
}

// ✅ Change Password
public function changePassword(Request $request)
{
    $request->validate([
        'current_password'          => 'required|string',
        'new_password'              => 'required|string|min:6|confirmed',
        // 'new_password_confirmation' is auto-checked by 'confirmed' rule
    ]);

    $user = $request->user();

    // Check current password
    if (! \Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'status'  => false,
            'message' => 'Current password is incorrect.',
        ], 422);
    }

    // Update password
    $user->update([
        'password' => \Hash::make($request->new_password),
    ]);

    // Optional: logout all other devices
    // $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

    return response()->json([
        'status'  => true,
        'message' => 'Password changed successfully.',
    ]);
}

}
