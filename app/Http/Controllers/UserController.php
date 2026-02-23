<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
 public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'pending',
            'otp' => rand(100000, 999999),
        ]);

        // Send OTP email
        \Mail::raw("Your OTP is: {$user->otp}", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP for Prosix Account');
        });

        return response()->json([
            'status' => true,
            'message' => 'Registered successfully. Please verify OTP sent to your email.',
            'email' => $user->email
        ], 201);
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

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 422);
        }

        $user->update([
            'status' => 'approved',
            'otp_verified_at' => now(),
            'otp' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. You can now login.'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if ($user->status != 'approved') {
            Auth::logout();
            return response()->json(['status' => false, 'message' => 'OTP verification required'], 403);
        }

        // Login successful
        return response()->json([
            'status' => true,
            'message' => 'Login successful'
        ]);
    }




    // Logout
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // Profile
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }
    // UserController.php





}
