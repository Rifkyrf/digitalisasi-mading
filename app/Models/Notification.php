<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'custom_notifications'; // Sesuaikan nama tabel

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'url',
        'read',
    ];

    // Relasi ke User (penerima notifikasi)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk mengambil notifikasi yang belum dibaca
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    // Scope untuk mengambil notifikasi berdasarkan type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}