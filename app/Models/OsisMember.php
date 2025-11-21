<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OsisMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'photo',
        'type',
        'order',
        'angkatan',
        'nama_sekbid',
    ];

    // Aksesori: URL foto lengkap
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://via.placeholder.com/200x250?text=No+Photo';
    }
}