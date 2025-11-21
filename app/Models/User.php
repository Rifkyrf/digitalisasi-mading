<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Work;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'nis',
        'password',
        'role',
        'profile_photo',
        'bio',
    ];

    use Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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

    // Cek role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    public function isGuest()
    {
        return $this->role === 'guest';
    }

    // Aksesori: URL foto profil
    public function getProfilePhotoUrlAttribute()
    {
        // Jika user upload foto, gunakan itu
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        // Gunakan sprintf untuk hindari error parsing string
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