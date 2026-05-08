<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
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

    // Show all users
   public function index()
{
    $users = User::orderByDesc('is_pinned')
        ->latest()
        ->get();

    return view('user_mangment.all_user', compact('users'));
}

    public function toggleStatus($id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->status == 'blocked') {
            $user->status = 'approved';
        } else {
            $user->status = 'blocked';
            DB::table('sessions')->where('user_id', $user->id)->delete();
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

    // ─────────────────────────────────────────
    // FORGOT PASSWORD — Form dikhao
    // ─────────────────────────────────────────
    public function showForgotForm()
    {
        return view('auth.admin-forgot-password');
    }

    // ─────────────────────────────────────────
    // FORGOT PASSWORD — Email bhejo (Brevo API)
    // ─────────────────────────────────────────
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Case-insensitive email search
        $admin = Admin::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

        // Security: hamesha success dikhao
        if (!$admin) {
            return back()->with('success', 'If this email exists, a reset link has been sent.');
        }

        // Purana token delete karo
        DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->delete();

        // Naya token generate karo — email lowercase mein save karo
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => strtolower($admin->email),
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $resetUrl = url('/admin/reset-password/' . $token . '?email=' . urlencode(strtolower($admin->email)));

        // Brevo API se email bhejo
        try {
            $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', env('BREVO_API_KEY'));

            $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
                new \GuzzleHttp\Client, $config
            );

            $htmlContent = view('emails.admin-reset-password', [
                'admin'    => $admin,
                'resetUrl' => $resetUrl,
            ])->render();

            $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                'subject'     => 'Prosix Admin — Password Reset Request',
                'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                'to'          => [['email' => 'sales@prosix.com']],
                'htmlContent' => $htmlContent,
            ]);

            $apiInstance->sendTransacEmail($email);

        } catch (\Exception $e) {
            Log::error('Reset email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Password reset link sent to your email!');
    }

    // ─────────────────────────────────────────
    // RESET PASSWORD — Form dikhao
    // ─────────────────────────────────────────
    public function showResetForm(Request $request, $token)
    {
        return view('auth.admin-reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // ─────────────────────────────────────────
    // RESET PASSWORD — Password update karo
    // ─────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Case-insensitive token check
        $record = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->first();

        if (!$record) {
            return back()->with('error', 'Invalid or expired reset link.');
        }

        // Token verify karo
        if (!Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Invalid or expired reset link.');
        }

        // Token 60 minute se purana ho toh reject karo
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
                ->delete();
            return back()->with('error', 'Reset link has expired. Please request a new one.');
        }

        // Case-insensitive admin find karo
        $admin = Admin::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

        if (!$admin) {
            return back()->with('error', 'Admin not found.');
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // Token delete karo — single use
        DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->delete();

        return redirect()->route('admin.login')
            ->with('success', 'Password reset successfully! Please login with your new password.');
    }
  public function togglePin(Request $request, $id)
{
    $user = User::findOrFail($id);

    if ($user->is_pinned) {
        // Unpin — label bhi clear
        $user->is_pinned       = false;
        $user->customer_label  = null;
    } else {
        // Pin — label save karo
        $user->is_pinned      = true;
        $user->customer_label = $request->input('customer_label', $user->name);
    }

    $user->save();

    return response()->json(['success' => true, 'is_pinned' => $user->is_pinned]);
}
public function customers()
{
    $customers = User::where('is_pinned', true)
        ->orderByDesc('created_at')
        ->get();

    return view('user_mangment.customer', compact('customers'));
}
}
