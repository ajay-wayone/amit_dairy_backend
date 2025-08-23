<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        $email = $request->email;
        $otp   = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(60);

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Insert new token and OTP
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => $token,
            'otp'        => $otp,
            'created_at' => now(),
        ]);

        // Send email with OTP (for now, just show in session)
        session(['otp_email' => $email, 'otp' => $otp]);

        return redirect()->route('admin.verify-otp')
            ->with('success', 'OTP sent to your email!)');
    }

    public function showVerifyOTP()
    {
        if (! session('otp_email')) {
            return redirect()->route('admin.forgot-password');
        }
        return view('admin.auth.verify-otp');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('otp_email');
        $otp   = session('otp');

        if ($request->otp === $otp) {
            return redirect()->route('admin.reset-password')
                ->with('success', 'OTP verified successfully!');
        }

        return back()->withErrors(['otp' => 'Invalid OTP']);
    }

    public function showResetForm()
    {
        if (! session('otp_email')) {
            return redirect()->route('admin.forgot-password');
        }
        return view('admin.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('otp_email');

        $admin = Admin::where('email', $email)->first();
        $admin->update(['password' => Hash::make($request->password)]);

        // Clear session
        session()->forget(['otp_email', 'otp']);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('admin.login')
            ->with('success', 'Password reset successfully!');
    }
}
