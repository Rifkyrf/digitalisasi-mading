<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Work;
use App\Models\Hakguna;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nis',
        'role', // foreign key ke hakgunas
        'profile_photo',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi ke Hakguna
    public function hakguna()
    {
        return $this->belongsTo(Hakguna::class, 'role');
    }

    // Relasi ke karya
    public function works()
    {
        return $this->hasMany(Work::class);
    }

    // Relasi ke notifikasi (baru)
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    // Cek role berdasarkan relasi
    public function isAdmin()
    {
        return $this->hakguna && $this->hakguna->name === 'admin';
    }

    public function isGuru()
    {
        return $this->hakguna && $this->hakguna->name === 'guru';
    }

    public function isSiswa()
    {
        return $this->hakguna && $this->hakguna->name === 'siswa';
    }

    public function isGuest()
    {
        return $this->hakguna && $this->hakguna->name === 'guest';
    }

    // Aksesori: URL foto profil
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return sprintf(
            'https://ui-avatars.com/api/?name=%s&background=0d47a1&color=fff&size=128',
            urlencode($this->name)
        );
    }

    // Hitung jumlah konten per tipe
    public function getContentTypeCountAttribute()
    {
        return [
            'karya' => $this->works()->where('type', 'karya')->count(),
            'mading' => $this->works()->where('type', 'mading')->count(),
            'berita' => $this->works()->where('type', 'berita')->count(),
        ];
    }
}