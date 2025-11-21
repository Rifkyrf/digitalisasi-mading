<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Otp;
use Carbon\Carbon;

class OtpController extends Controller
{
    /**
     * Generate dan kirim OTP ke nomor WhatsApp
     */
    public function sendOtp(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^628[0-9]{9,11}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nomor HP tidak valid. Harus dimulai dengan 628 dan panjang 12-14 digit.'
            ], 422);
        }

        $phone = $request->phone;

        // Generate OTP 6 digit
        $otpCode = random_int(100000, 999999);

        // Hitung waktu kadaluarsa (5 menit)
        $expiresAt = Carbon::now()->addMinutes(5);

        // Simpan ke database
        $otp = Otp::updateOrCreate(
            ['phone' => $phone, 'used' => false],
            [
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt,
                'used' => false
            ]
        );

        // Kirim OTP via Fonnte
        $response = $this->sendWhatsAppMessage($phone, $otpCode);

        if ($response['success']) {
            return response()->json([
                'message' => 'OTP berhasil dikirim ke WhatsApp Anda.',
                'phone' => $phone,
                'expires_at' => $expiresAt->toDateTimeString()
            ]);
        } else {
            return response()->json([
                'error' => 'Gagal mengirim OTP. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp'   => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Input tidak valid.'], 422);
        }

        $phone = $request->phone;
        $otpCode = $request->otp;

        // Cari OTP yang belum digunakan dan belum kadaluarsa
        $otp = Otp::where('phone', $phone)
                   ->where('otp_code', $otpCode)
                   ->where('used', false)
                   ->where('expires_at', '>', Carbon::now())
                   ->first();

        if (!$otp) {
            return response()->json(['error' => 'OTP tidak valid atau sudah kadaluarsa.'], 400);
        }

        // Tandai sebagai sudah digunakan
        $otp->used = true;
        $otp->save();

        // Jika berhasil, bisa lanjut ke reset password
        return response()->json([
            'message' => 'OTP terverifikasi! Anda bisa lanjutkan proses reset password.',
            'token' => $this->generateResetToken($phone) // Token sementara untuk reset password
        ]);
    }

    /**
     * Fungsi helper: Kirim pesan WhatsApp via Fonnte API
     */
    private function sendWhatsAppMessage($phone, $otpCode)
    {
        $apiKey = env('FONNTE_API_KEY'); // Pastikan sudah diisi di .env

        $url = 'https://api.fonnte.com/send';
        $payload = [
            'target' => $phone, // Format: 6281234567890
            'message' => "Kode OTP untuk reset password Anda:\n\n*{$otpCode}*\n\nKode ini berlaku selama 5 menit. Jangan berikan ke siapa pun.",
            'countryCode' => '62', // Optional, tapi disarankan
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return ['success' => true];
            } else {
                \Log::error('Fonnte API Error: ' . $response->body());
                return ['success' => false, 'error' => $response->body()];
            }
        } catch (\Exception $e) {
            \Log::error('Fonnte API Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fungsi helper: Generate token reset password sementara (bisa pakai JWT atau string acak)
     */
    private function generateResetToken($phone)
    {
        // Contoh sederhana: gunakan hash dari phone + timestamp
        return hash('sha256', $phone . time() . Str::random(20));
    }
}