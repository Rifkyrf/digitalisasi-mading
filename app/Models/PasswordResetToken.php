<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    protected $table = 'otp_password_resets'; // Gunakan tabel baru

    protected $fillable = [
        'email',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Otomatis hapus token lama saat membuat baru
    protected static function booted()
    {
        static::creating(function ($model) {
            self::where('email', $model->email)->delete();
        });
    }
}