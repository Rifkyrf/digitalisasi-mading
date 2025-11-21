<?php

namespace App\Http\Controllers;

use App\Models\Work;

class PageController extends Controller
{
    /**
     * Halaman utama publik (hanya published).
     */
    public function landing()
    {
        $query = Work::published()->with('user');

        $type = request('type');
        if ($type && in_array($type, ['karya', 'mading', 'harian', 'mingguan', 'prestasi', 'opini', 'event'])) {
            $query->where('type', $type);
        }

        $works = $query->latest()->paginate(10);

        // Ambil 3 karya terpopuler
        $popularWorks = Work::published()
            ->with('user')
            ->orderBy('created_at', 'desc') // Ganti dengan like count jika sudah ada fitur like
            ->limit(3)
            ->get();

        return view('pages.landing', compact('works', 'popularWorks'));
    }

    /**
     * Halaman karya-karya populer.
     */
    public function popular()
    {
        $popularWorks = Work::published()
            ->with('user')
            ->orderBy('created_at', 'desc') // Ganti dengan like count jika sudah ada fitur like
            ->paginate(10);

        return view('pages.popular', compact('popularWorks'));
    }
}