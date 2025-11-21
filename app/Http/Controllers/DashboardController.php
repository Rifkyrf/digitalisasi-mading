<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ArticlesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isGuru() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        // Statistik Karya
        $draftCount = Work::draft()->count();
        $publishedCount = Work::published()->count();

        // Jenis File Terbanyak
        $fileTypeData = Work::select('file_type', DB::raw('count(*) as count'))
            ->whereNotNull('file_type')
            ->groupBy('file_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Jenis Konten Terbanyak
        $contentTypeData = Work::select('content_type', DB::raw('count(*) as count'))
            ->whereNotNull('content_type')
            ->groupBy('content_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Pembagian Pengguna
        $roleData = User::select('role', DB::raw('count(*) as count'))
            ->whereIn('role', ['siswa', 'guru', 'admin','guest'])
            ->groupBy('role')
            ->get();

        // Total Pengguna & Komentar
        $totalUsers = User::count();
        $totalComments = Comment::count();

        // Data untuk Tabel Artikel dengan PAGINATION
        $articles = Work::with('user')
            ->select('id', 'title', 'content_type', 'file_type', 'status', 'type', 'created_at')
            ->latest();

        // Jika ada parameter search
        if ($search = request('search')) {
            $articles->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content_type', 'like', '%' . $search . '%')
                  ->orWhere('file_type', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // GANTI get() MENJADI paginate(10)
        $articles = $articles->paginate(10);

        return view('dashboard.statistik', compact(
            'draftCount',
            'publishedCount',
            'fileTypeData',
            'contentTypeData',
            'roleData',
            'totalUsers',
            'totalComments',
            'articles'
        ));
    }

    public function exportExcel()
    {
        // Gunakan kelas export Anda
        return Excel::download(new ArticlesExport, 'Laporan_Artikel_' . date('Y-m-d_His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        // Ambil data artikel
        $articles = Work::with('user')->orderBy('created_at', 'desc')->get();

        // Hitung statistik untuk kesimpulan dan diagram
        $draftCount = Work::draft()->count();
        $publishedCount = Work::published()->count();
        // Kamu bisa tambahkan hitungan lain di sini jika ingin menampilkan diagram tambahan
        // $totalUsers = User::count();
        // $totalComments = Comment::count();
        // $fileTypeData = Work::select('file_type', DB::raw('count(*) as count'))->whereNotNull('file_type')->groupBy('file_type')->orderByDesc('count')->get();
        // $contentTypeData = Work::select('content_type', DB::raw('count(*) as count'))->whereNotNull('content_type')->groupBy('content_type')->orderByDesc('count')->get();
        // $roleData = User::select('role', DB::raw('count(*) as count'))->whereIn('role', ['siswa', 'guru', 'admin'])->groupBy('role')->get();

        $data = [
            'articles' => $articles,
            'draftCount' => $draftCount, // Tambahkan ini
            'publishedCount' => $publishedCount, // Tambahkan ini
            // 'totalUsers' => $totalUsers, // Tambahkan jika diperlukan
            // 'totalComments' => $totalComments, // Tambahkan jika diperlukan
            // 'fileTypeData' => $fileTypeData, // Tambahkan jika diperlukan
            // 'contentTypeData' => $contentTypeData, // Tambahkan jika diperlukan
            // 'roleData' => $roleData, // Tambahkan jika diperlukan
        ];

        $pdf = Pdf::loadView('dashboard.pdf', $data);
        return $pdf->download('laporan_artikel_' . date('Y-m-d') . '.pdf');
    }
}