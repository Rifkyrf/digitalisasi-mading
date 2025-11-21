<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Messages\MailMessage;


class AppServiceProvider extends ServiceProvider
{
    public function boot()
{
    ResetPassword::toMailUsing(function ($notifiable, $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Notifikasi Reset Password')
            ->line('Kamu menerima email ini karena kami menerima permintaan untuk mengatur ulang kata sandi akunmu.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Tautan ini akan kadaluarsa dalam ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' menit.')
            ->line('Jika kamu tidak mengajukan permintaan ini, abaikan email ini.');
    });
}
}

