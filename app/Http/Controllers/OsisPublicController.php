<?php

namespace App\Http\Controllers;

use App\Models\OsisMember;
use Illuminate\Http\Request;

class OsisPublicController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua angkatan yang tersedia
        $angkatanList = OsisMember::distinct()->pluck('angkatan')->sortDesc();

        // 2. Tentukan angkatan aktif
        if ($request->filled('angkatan')) {
            $angkatanAktif = $request->angkatan;
        } else {
            // Ambil angkatan terbaru dari data yang benar-benar ada
            $angkatanAktif = $angkatanList->first() ?? '2024/2025';
        }

        // 3. Tentukan Urutan Peran (Role) Sesuai Hierarki
        // Nilai harus sesuai dengan ENUM di tabel osis_members
        $roleOrder = [
            'ketua',
            'wakil ketua',
            'sekretaris',
            'bendahara',
            'anggota', 
        ];

        // Buat string untuk orderByRaw menggunakan FIELD()
        // Ini akan memastikan urutan sesuai array di atas, bukan alfabetis
        $orderByRole = "FIELD(role, '" . implode("','", $roleOrder) . "')";

        // 4. Ambil data Inti OSIS
        // Urutkan berdasarkan hierarki Role terlebih dahulu.
        $intiOsis = OsisMember::where('type', 'inti')
                             ->where('angkatan', $angkatanAktif)
                             ->orderByRaw($orderByRole) // Pengurutan utama berdasarkan hierarki peran
                             ->orderBy('order')        // Pengurutan sekunder (untuk membedakan Sekre I/II atau Benda I/II)
                             ->get();

        // 5. Ambil data Sekbid
        // Urutkan berdasarkan Nama Sekbid (Nama Bidang) terlebih dahulu,
        // kemudian berdasarkan hierarki Role (misalnya: Koordinator, Anggota Sekbid),
        // dan terakhir berdasarkan nama anggota.
        $sekbid = OsisMember::where('type', 'sekbid')
                            ->where('angkatan', $angkatanAktif)
                            ->orderBy('nama_sekbid')
                            ->orderByRaw($orderByRole)
                            ->orderBy('name')
                            ->get();

        return view('pages.osis', compact('intiOsis', 'sekbid', 'angkatanAktif', 'angkatanList'));
    }
}