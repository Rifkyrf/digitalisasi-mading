<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtpToken extends Model
{
    protected $table = 'user_otp_tokens';

    protected $fillable = ['email', 'token', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}