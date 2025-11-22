<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\User;
use App\Models\Comment;
use App\Models\Hakguna;
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

        // Pembagian Pengguna berdasarkan hakguna
        $roleData = Hakguna::withCount('users')->get();

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
        return Excel::download(new ArticlesExport, 'Laporan_Artikel_' . date('Y-m-d_His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $articles = Work::with('user')->orderBy('created_at', 'desc')->get();

        $draftCount = Work::draft()->count();
        $publishedCount = Work::published()->count();

        $data = [
            'articles' => $articles,
            'draftCount' => $draftCount,
            'publishedCount' => $publishedCount,
        ];

        $pdf = Pdf::loadView('dashboard.pdf', $data);
        return $pdf->download('laporan_artikel_' . date('Y-m-d') . '.pdf');
    }
}