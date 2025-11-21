<?php

namespace App\Http\Controllers;

use App\Mail\DraftSubmitted;
use App\Mail\WorkPublished;
use App\Models\Notification;
use App\Models\User;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Impor model User untuk mengirim email ke admin/guru
use Illuminate\Support\Facades\Storage; // Impor facade Mail
use Illuminate\Validation\Rule; // Impor mail class draft
use Illuminate\Validation\ValidationException; // Impor mail class publikasi
use Mail; // Impor model notifikasi custom

class WorkController extends Controller
{
    /**
     * Tampilkan daftar karya yang dipublikasikan (halaman publik).
     */
    public function index()
    {
        $works = Work::published()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('works.index', compact('works'));
    }

    /**
     * Tampilkan form upload.
     */
    public function create()
    {
        $types = [
            'karya' => 'Karya Siswa',
            'mading' => 'Mading Digital',
            'mingguan' => 'weekly',
            'harian' => 'daily',
            'prestasi' => 'prestasi',
            'opini' => 'opini',
            'event' => 'event',

        ];

        // Admin/Guru bisa mengakses semua tipe
        if (Auth::check() && (Auth::user()->isGuru() || Auth::user()->isAdmin())) {
            // Opsional: tambahkan tipe lain di sini nanti
        }

        return request()->ajax()
            ? view('works._form_upload', compact('types'))
            : view('works.upload', compact('types'));
    }

    /**
     * Simpan karya baru (default status = draft, kecuali untuk admin).
     */
    public function store(Request $request)
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Silakan login.'], 401)
                : redirect('/login')->with('error', 'Akses ditolak: Silakan login.');
        }

        $allowedTypes = ['karya', 'mading', 'harian', 'mingguan', 'opini', 'prestasi', 'event'];
        if (Auth::user()->isAdmin()) {
            // Izinkan semua tipe untuk guru/admin
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                // Perbarui aturan validasi file untuk mengizinkan ekstensi tambahan
                'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,mkv,mp3,wav,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,py,js,html,css,php,java,cpp,json,xml,yml,md,zip,rar,exe,apk|max:512000', // max 500MB
                'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // max 2MB
                'type' => ['required', Rule::in($allowedTypes)],
            ], [
                'file.max' => 'File utama maksimal 500MB.',
                'thumbnail.max' => 'Thumbnail maksimal 2MB.',
                'file.mimes' => 'File harus berupa gambar, video, audio, dokumen, kode, arsip, executable (.exe), atau APK (.apk).',
            ]);
        } catch (ValidationException $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422)
                : throw $e;
        }

        $file = $request->file('file');
        $originalPath = $file->store('uploads', 'public');
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        } elseif (in_array($extension, ['mp4', 'webm', 'mov', 'avi'])) {
            $thumbnailPath = 'placeholders/video.jpg';
        }

        // Tentukan status: 'published' jika admin, 'draft' jika tidak
        $status = 'draft';
        if (Auth::user()->isAdmin()) {
            $status = 'published';
        }

        $work = Work::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $originalPath,
            'file_type' => $extension,
            'content_type' => $this->determineContentType($extension),
            'mime_type' => $mimeType,
            'user_id' => Auth::id(),
            'thumbnail_path' => $thumbnailPath,
            'type' => $request->type,
            'status' => $status, // Gunakan status yang telah ditentukan
        ]);

        Log::info('Karya diunggah', [
            'work_id' => $work->id,
            'user_id' => Auth::id(),
            'title' => $work->title,
            'status' => $work->status, // Log status untuk verifikasi
        ]);

        // --- Tambahkan logika email untuk draft baru ---
        if ($work->status === 'draft') { // Hanya kirim email jika statusnya draft
            Log::info('Mengirim email notifikasi draft baru', [
                'work_id' => $work->id,
                'work_title' => $work->title,
            ]);

            // Ambil semua user dengan role admin atau guru
            $adminsAndGurus = User::where(function ($query) {
                $query->where('role', 'admin')
                    ->orWhere('role', 'guru');
            })->get();

            Log::info('Menemukan '.$adminsAndGurus->count().' admin/guru untuk dikirimi email.', ['users' => $adminsAndGurus->pluck('email')->toArray()]);

            // Kirim email ke setiap admin dan guru
            foreach ($adminsAndGurus as $user) {
                Log::info('Mengirim email ke: '.$user->name.' ('.$user->email.')');
                try {
                    Mail::to($user->email)
                        ->send(new DraftSubmitted($work));
                    Log::info('Email draft berhasil dikirim ke: '.$user->name);
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email draft ke '.$user->name.': '.$e->getMessage());
                }
            }

            // --- Tambahkan logika notifikasi database untuk draft baru ---
            Log::info('Menyimpan notifikasi database untuk draft baru', [
                'work_id' => $work->id,
                'work_title' => $work->title,
            ]);

            Log::info('Menemukan '.$adminsAndGurus->count().' admin/guru untuk ditambahkan notifikasinya.', ['users' => $adminsAndGurus->pluck('email')->toArray()]);

            // Simpan notifikasi ke database untuk setiap admin dan guru
            foreach ($adminsAndGurus as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Draft Baru Dikirim',
                    'message' => 'Siswa "'.$work->user->name.'" mengirim draft baru: "'.$work->title.'"',
                    'type' => 'draft_submitted',
                    'url' => route('moderator.show', $work), // Ganti dengan route yang benar jika perlu
                ]);
                Log::info('Notifikasi database dibuat untuk: '.$user->name);
            }
            // --- Akhir tambahan notifikasi database draft ---
        }
        // --- Akhir tambahan email draft ---

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Karya berhasil diunggah! tunggu admin unuk verifikasi', 'work_id' => $work->id])
            : redirect('/dashboard')->with('success', 'Karya berhasil diunggah! tunggu admin untuk verifikasi');
    }

    /**
     * Tampilkan form edit (halaman penuh).
     */
    public function edit($id)
    {
        $work = Work::with('user')->findOrFail($id);
        if ($work->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Tidak diizinkan.');
        }

        $types = [
            'karya' => 'Karya Siswa',
            'mading' => 'Mading Digital',
            'mingguan' => 'weekly',
            'harian' => 'daily',
            'prestasi' => 'prestasi',
            'opini' => 'opini',
            'event' => 'event',
        ];

        return view('works.edit', compact('work', 'types'));
    }

    /**
     * Update karya (NON AJAX).
     */
    public function update(Request $request, Work $work)
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Silakan login.'], 401);
            }

            return redirect('/login');
        }

        // Hanya pemilik atau admin yang bisa update
        if ($work->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
            }

            return redirect('/dashboard')->with('error', 'Tidak diizinkan.');
        }

        $allowedTypes = ['karya', 'mading'];
        if (Auth::user()->isGuru() || Auth::user()->isAdmin()) {
            $allowedTypes = array_merge($allowedTypes, ['karya', 'mading', 'harian', 'mingguan', 'opini', 'prestasi', 'event']);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ['required', Rule::in($allowedTypes)],
        ];

        // Jika ada file baru, tambahkan validasi
        if ($request->hasFile('file')) {
            // Perbarui aturan validasi file untuk update juga
            $rules['file'] = 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,mkv,mp3,wav,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,py,js,html,css,php,java,cpp,json,xml,yml,md,zip,rar,exe,apk|max:512000'; // max 500MB
        }
        if ($request->hasFile('thumbnail')) {
            $rules['thumbnail'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'; // max 2MB
        }

        try {
            $request->validate($rules, [
                'file.max' => 'File maksimal 500MB.',
                'thumbnail.max' => 'Thumbnail maksimal 2MB.',
                'file.mimes' => 'File harus berupa gambar, video, audio, dokumen, kode, arsip, executable (.exe), atau APK (.apk).', // Pesan error tambahan
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        // Update data dasar
        $work->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        // Jika ada file baru
        if ($request->hasFile('file')) {
            // Hapus file lama (opsional)
            if ($work->file_path && Storage::disk('public')->exists($work->file_path)) {
                Storage::disk('public')->delete($work->file_path);
            }

            $newFile = $request->file('file');
            $filePath = $newFile->store('uploads', 'public');
            $extension = strtolower($newFile->getClientOriginalExtension());

            $work->update([
                'file_path' => $filePath,
                'file_type' => $extension,
                'content_type' => $this->determineContentType($extension),
                'mime_type' => $newFile->getMimeType(),
            ]);
        }

        // Jika ada thumbnail baru
        if ($request->hasFile('thumbnail')) {
            if ($work->thumbnail_path && Storage::disk('public')->exists($work->thumbnail_path)) {
                Storage::disk('public')->delete($work->thumbnail_path);
            }
            $thumbPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $work->update(['thumbnail_path' => $thumbPath]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => '🎉 Berhasil diperbarui!']);
        }

        return redirect()->route('work.show', $work->id)->with('success', '🎉 Berhasil diperbarui!');
    }

    /**
     * Update karya.
     */
    // prototype untuk ajax
    // public function update1(Request $request, Work $work)
    // {
    //     if (!Auth::check() || ($work->user_id !== Auth::id() && !Auth::user()->isAdmin())) {
    //         return $request->expectsJson()
    //             ? response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403)
    //             : redirect('/dashboard')->with('error', 'Tidak diizinkan.');
    //     }

    //     $allowedTypes = ['karya', 'mading', 'harian', 'mingguan'];

    //     $rules = [
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string|max:1000',
    //         'type' => ['required', Rule::in($allowedTypes)],
    //     ];

    //     if ($request->hasFile('file')) {
    //         $rules['file'] = 'required|file|max:512000';
    //     }
    //     if ($request->hasFile('thumbnail')) {
    //         $rules['thumbnail'] = 'nullable|image|max:2048';
    //     }

    //     $request->validate($rules, [
    //         'file.max' => 'File maksimal 500MB.',
    //         'thumbnail.max' => 'Thumbnail maksimal 2MB.',
    //     ]);

    //     $work->update([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'type' => $request->type,
    //     ]);

    //     if ($request->hasFile('file')) {
    //         if ($work->file_path && Storage::disk('public')->exists($work->file_path)) {
    //             Storage::disk('public')->delete($work->file_path);
    //         }
    //         $newFile = $request->file('file');
    //         $extension = strtolower($newFile->getClientOriginalExtension());
    //         $work->update([
    //             'file_path' => $newFile->store('uploads', 'public'),
    //             'file_type' => $extension,
    //             'content_type' => $this->determineContentType($extension),
    //             'mime_type' => $newFile->getMimeType(),
    //         ]);
    //     }

    //     if ($request->hasFile('thumbnail')) {
    //         if ($work->thumbnail_path && Storage::disk('public')->exists($work->thumbnail_path)) {
    //             Storage::disk('public')->delete($work->thumbnail_path);
    //         }
    //         $work->update([
    //             'thumbnail_path' => $request->file('thumbnail')->store('thumbnails', 'public')
    //         ]);
    //     }

    //     return $request->expectsJson()
    //         ? response()->json(['success' => true, 'message' => 'Berhasil diperbarui!'])
    //         : redirect()->route('work.show', $work->id)->with('success', 'Berhasil diperbarui!');
    // }

    /**
     * Tampilkan detail karya (hanya published).
     */
    public function show($id)
    {
        $work = Work::published()->with('user')->findOrFail($id);
        $comments = $work->comments()->with('user')->latest()->get();
        $userLiked = Auth::check() && $work->likes()->where('user_id', Auth::id())->exists();

        return view('works.show', compact('work', 'comments', 'userLiked'));
    }

    /**
     * Tampilan modal detail (hanya published).
     */
    public function showModal(Work $work)
    {
        if ($work->status !== 'published') {
            abort(404);
        }

        $work->loadMissing('user');
        $likesCount = $work->likes()->count();
        $comments = $work->comments()->with('user')->latest()->limit(3)->get();
        $userLiked = Auth::check() && $work->likes()->where('user_id', Auth::id())->exists();

        return view('works._modal_content', compact('work', 'userLiked', 'comments', 'likesCount'));
    }

    /**
     * Hapus karya.
     */
    public function destroy(Work $work)
    {
        if (! Auth::check() || ($work->user_id !== Auth::id() && ! Auth::user()->isAdmin() && ! Auth::user()->isguru())) {
            return redirect()->back()->with('error', 'Tidak diizinkan.');
        }

        if ($work->file_path && Storage::disk('public')->exists($work->file_path)) {
            Storage::disk('public')->delete($work->file_path);
        }
        if ($work->thumbnail_path && Storage::disk('public')->exists($work->thumbnail_path)) {
            Storage::disk('public')->delete($work->thumbnail_path);
        }

        $work->delete();

        return redirect('/dashboard')->with('success', 'Karya berhasil dihapus!');
    }

    // === MODERASI (Admin & Guru) ===

    /**
     * Daftar draft untuk moderasi.
     */
    public function drafts(Request $request)
    {
        if (! Auth::check() || ! (Auth::user()->isAdmin() || Auth::user()->isGuru())) {
            abort(403);
        }

        $drafts = Work::draft()
            ->with('user')
            ->latest()
            ->paginate(15);

        $query = Work::with('user'); // Mulai dari semua karya

        // Tambahkan pencarian jika ada parameter 'search'
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('type', 'like', "%{$search}%"); // Mencari berdasarkan type (bukan label)
            });
        }

        $works = $query->latest()->paginate(15);

        return view('moderasi.drafts', compact('drafts', 'works'));
    }

    /**
     * Publish draft menjadi published.
     */
    public function publish(Work $work)
    {
        if (! Auth::check() || ! (Auth::user()->isAdmin() || Auth::user()->isGuru())) {
            abort(403);
        }

        if ($work->status !== 'draft') {
            return back()->with('error', 'Artikel ini sudah dipublikasikan.');
        }

        $work->update(['status' => 'published']);

        Log::info('Karya berhasil dipublikasikan, mencoba mengirim notifikasi WorkPublished', [
            'work_id' => $work->id,
            'work_title' => $work->title,
            'author_email' => $work->user->email,
        ]);

        // --- Tambahkan logika email untuk karya dipublikasikan ---
        try {
            Mail::to($work->user->email)
                ->send(new WorkPublished($work));
            Log::info('Email publikasi berhasil dikirim ke: '.$work->user->name.' ('.$work->user->email.')');
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email publikasi ke '.$work->user->name.': '.$e->getMessage());
        }
        // --- Akhir tambahan email publikasi ---

        // --- Tambahkan logika notifikasi database untuk karya dipublikasikan ---
        Log::info('Menyimpan notifikasi database untuk publikasi karya', [
            'work_id' => $work->id,
            'work_title' => $work->title,
            'author_id' => $work->user_id,
        ]);

        // Simpan notifikasi ke database untuk penulis karya
        Notification::create([
            'user_id' => $work->user_id,
            'title' => 'Karya Dipublikasikan',
            'message' => 'Karya Anda "'.$work->title.'" telah berhasil dipublikasikan.',
            'type' => 'work_published',
            'url' => route('work.show', $work), // Ganti dengan route yang benar jika perlu
        ]);
        Log::info('Notifikasi database publikasi dibuat untuk penulis: '.$work->user->name);
        // --- Akhir tambahan notifikasi database publikasi ---

        return back()->with('success', 'Artikel berhasil dipublikasikan!');
    }

    /**
     * Unpublish karya menjadi draft.
     */
    public function unpublish(Work $work)
    {
        if (! Auth::check() || ! (Auth::user()->isAdmin() || Auth::user()->isGuru())) {
            abort(403);
        }

        if ($work->status !== 'published') {
            return back()->with('error', 'Artikel ini belum dipublikasikan.');
        }

        $work->update(['status' => 'draft']);

        // Logika notifikasi bisa ditambahkan di sini jika diperlukan
        // Contoh: Kirim notifikasi ke penulis bahwa karyanya tidak dipublikasikan

        return back()->with('success', 'Publikasi artikel berhasil dibatalkan dan dikembalikan ke draft.');
    }

    // === HELPER ===

    private function determineContentType($extension)
    {
        $map = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'video' => ['mp4', 'webm', 'mov', 'avi', 'mkv', 'flv'],
            'document' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'],
            'code' => ['py', 'js', 'html', 'css', 'php', 'java', 'cpp', 'json', 'xml', 'yml', 'md'],
        ];

        foreach ($map as $type => $exts) {
            if (in_array($extension, $exts)) {
                return $type;
            }
        }

        return 'file';
    }

    /**
     * Tampilkan detail karya untuk moderator (bisa lihat draft juga).
     */
    public function moderatorShow(Work $work)
    {
        // Hanya admin atau guru yang boleh lihat draft
        if (! Auth::check() || ! (Auth::user()->isAdmin() || Auth::user()->isGuru())) {
            abort(403);
        }

        // Load relasi user
        $work->loadMissing('user');

        // Ambil komentar (opsional, jika ingin ditampilkan juga)
        // $comments = $work->comments()->with('user')->latest()->get();
        // $userLiked = Auth::check() && $work->likes()->where('user_id', Auth::id())->exists();

        return view('moderasi.preview', compact('work'));
    }
}
