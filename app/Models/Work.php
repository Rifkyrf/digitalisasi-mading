<?php

namespace App\Models;

use App\Models\user;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Work extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'content_type',
        'mime_type',
        'user_id',
        'thumbnail_path',
        'type',
        'status',
    ];

       // Scope untuk hanya ambil yang sudah dipublikasikan
       public function scopePublished($query)
       {
           return $query->where('status', 'published');
       }

       public function scopeDraft($query)
       {
           return $query->where('status', 'draft');
       }



    // Aksesori untuk URL file
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    // Aksesori untuk ikon berdasarkan tipe
    public function getIconAttribute()
    {
        $map = [
            'image' => 'icons/image.png',
            'video' => 'icons/video.png',
            'document' => 'icons/document.png',
            'code' => 'icons/code.png',
            'file' => 'icons/file.png',
        ];
        $path = $map[$this->content_type] ?? 'icons/file.png';
        return asset('storage/' . $path);
    }

    protected $appends = ['type_label', 'thumbnail_url'];

    public function getTypeLabelAttribute()
    {
        $labels = [
            'karya' => 'Karya Siswa',
            'mading' => 'Mading Digital',
            'mingguan' => 'weekly',
            'harian' => 'daily',
            'prestasi' => 'prestasi',
            'opini' => 'opini',
            'event' => 'event',
        ];
        return $labels[$this->type] ?? 'Konten';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getThumbnailUrlAttribute()
    {
        // Cek apakah thumbnail_path diisi
        if ($this->thumbnail_path) {
            // Jika ya, kembalikan URL untuk thumbnail yang disimpan
            return asset('storage/' . $this->thumbnail_path);
        }

        // Jika tidak, kembalikan ikon default berdasarkan tipe file
        // Gunakan accessor yang sudah ada
        return $this->thumbnail; // Ini akan memanggil getThumbnailAttribute()
    }

    // Aksesori untuk ikon thumbnail berdasarkan tipe file
    public function getThumbnailAttribute()
    {
        $ext = strtolower($this->file_type);
        $map = [
            'jpg' => 'thumbnails/image.png',
            'jpeg' => 'thumbnails/image.png',
            'png' => 'thumbnails/image.png',
            'gif' => 'thumbnails/image.png',
            'pdf' => 'thumbnails/pdf.png',
            'doc' => 'thumbnails/doc.png',
            'docx' => 'thumbnails/doc.png',
            'zip' => 'thumbnails/archive.png',
            'rar' => 'thumbnails/archive.png',
            'mp4' => 'thumbnails/video.png',
            'py' => 'thumbnails/code.png',
            'js' => 'thumbnails/code.png',
            'html' => 'thumbnails/code.png',
            'txt' => 'thumbnails/code.png',
        ];

        $imagePath = $map[$ext] ?? 'thumbnails/file.png';
        return asset('storage/' . $imagePath);
    }

}
