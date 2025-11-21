<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.request-otp'); // view untuk memasukkan email
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        // Generate OTP (6 digit)
        $otp = rand(100000, 999999);

        // Hapus token lama jika ada
        PasswordResetToken::where('email', $email)->delete();

        // Simpan token baru ke tabel otp_password_resets
        PasswordResetToken::create([
            'email' => $email,
            'token' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        try {
            // Kirim OTP via email
            Mail::send('mails.otp', ['otp' => $otp], function ($m) use ($email) {
                $m->to($email)->subject('Kode OTP Reset Password');
            });

            // ✅ Redirect ke halaman verifikasi OTP dengan email di session
            return redirect()->route('password.otp.verify')
                             ->with('email', $email)
                             ->with('message', 'Kode OTP telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            // Hapus token jika gagal kirim email
            PasswordResetToken::where('email', $email)->delete();

            return back()->withErrors(['email' => 'Gagal mengirim OTP.']);
        }
    }

    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $token = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$token) {
            return back()->withErrors(['otp' => 'OTP tidak valid atau sudah kadaluarsa.']);
        }

        // Simpan email ke session untuk langkah berikutnya
        session(['reset_email' => $request->email]);

        return redirect()->route('password.otp.reset.form');
    }

    public function showResetForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.otp.request');
        }

        // ✅ Ganti ke view baru
        return view('auth.otp-reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('login')->withErrors(['error' => 'Sesi tidak valid.']);
        }

        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token yang digunakan
        PasswordResetToken::where('email', $email)->delete();

        // Hapus session
        session()->forget('reset_email');

        return redirect()->route('login')->with('status', 'Password berhasil direset!');
    }
}