<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\OsisController;
use App\Http\Controllers\OsisPublicController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Exports\ArticlesExport;
use App\Http\Controllers\NotificationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page (publik)
Route::get('/', [PageController::class, 'landing'])->name('home');

// Detail karya (publik) - GUNAKAN /works/ UNTUK SEMUA
Route::get('/works/{id}', [WorkController::class, 'show'])->name('work.show');
Route::get('/works/{work}/modal', [WorkController::class, 'showModal'])->name('work.modal');
// Route::get('/works/{id}', [WorkController::class, 'showg'])->name('work.showg');

// Like (butuh auth)
Route::post('/works/{work}/like', [LikeController::class, 'toggle'])->name('likes.toggle');

// Pencarian (publik)
Route::get('/search/results', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');
    $users = [];

    if (strlen($query) >= 2) {
        $users = \App\Models\User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('nis', 'like', "%{$query}%")
            ->select('id', 'name', 'profile_photo')
            ->limit(20)
            ->get();
    }

    return view('search.results', compact('users', 'query'));
})->name('search.results');

Route::get('/search/users', [SearchController::class, 'users'])->name('search.users');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (User & Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [WorkController::class, 'index'])->name('dashboard');

    // // Edit - prototypr ajax
    // Route::get('/works/{work}/edit/form', [WorkController::class, 'editForm'])->name('work.edit.form');
    // Route::put('/works/{work}', [WorkController::class, 'update'])->name('work.update');

    // Delete
    Route::delete('/works/{work}', [WorkController::class, 'destroy'])->name('work.destroy');

    // edit dan update
    Route::get('/works/{work}/edit', [WorkController::class, 'edit'])->name('works.edit');
    Route::put('/works/{work}', [WorkController::class, 'update'])->name('works.update');

    // Komentar
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');

    // Profil
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/work/{id}/edit', [WorkController::class, 'edit'])->name('work.edit');
});

Route::middleware(['auth','role:admin,guru,siswa'])->group(function () {
        // Upload
        Route::get('/upload', [WorkController::class, 'create'])->name('upload.page');
        Route::post('/upload', [WorkController::class, 'store'])->name('upload.store');
        Route::get('/upload/form', [WorkController::class, 'create'])->name('upload.form.modal');
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/users/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('/admin/import', [AdminController::class, 'importForm'])->name('admin.import.form');
    Route::post('/admin/import', [AdminController::class, 'import'])->name('admin.import');
});
// === Halaman Publik OSIS ===
Route::get('/osis', [OsisPublicController::class, 'index'])->name('osis.index');

// === Kelola OSIS (hanya admin/guru) ===
Route::middleware(['auth', 'role:admin,guru'])->prefix('admin')->name('osis.')->group(function () {
    Route::get('/osis', [OsisController::class, 'manage'])->name('manage');
    Route::get('/osis/create', [OsisController::class, 'create'])->name('create');
    Route::post('/osis', [OsisController::class, 'store'])->name('store');
    Route::get('/osis/{member}/edit', [OsisController::class, 'edit'])->name('edit');
    Route::put('/osis/{member}', [OsisController::class, 'update'])->name('update');
    Route::delete('/osis/{member}', [OsisController::class, 'destroy'])->name('destroy');
});

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);


Route::middleware(['web'])->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// === Moderasi Draft (hanya admin & guru) ===
Route::middleware(['auth', 'role:admin,guru'])->prefix('moderasi')->group(function () {
    Route::get('/drafts', [WorkController::class, 'drafts'])->name('moderasi.drafts');
    Route::post('/{work}/publish', [WorkController::class, 'publish'])->name('moderasi.publish');
    Route::post('/{work}/unpublish', [WorkController::class, 'unpublish'])->name('moderasi.unpublish'); // Ganti ke POST

});

// Route untuk preview (diluar prefix moderasi agar tidak bentrok)
Route::middleware(['auth', 'role:admin,guru'])->get('/moderator/works/{work}', [WorkController::class, 'moderatorShow'])->name('moderator.show');

Route::get('/search/results', [SearchController::class, 'results'])->name('search.results');
Route::get('/search/users', [SearchController::class, 'users'])->name('search.users');
Route::get('/search/all', [SearchController::class, 'searchAll']);


// Route untuk dashboard statistik, hanya bisa diakses oleh dan admin
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/statistik', [DashboardController::class, 'index']) // Tambahkan namespace lengkap
        ->name('dashboard.statistik')
        ->middleware('role:admin'); // Pastikan role ini benar, atau ubah ke 'role:admin|guru'
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])
        ->name('dashboard.export.excel')
        ->middleware('role:admin');
});

Route::put('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->middleware('auth');
Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->middleware('auth');
use App\Http\Controllers\PasswordResetController;


// Password Reset via otp
// Sistem Custom OTP Anda
Route::get('/password/otp/request', [PasswordResetController::class, 'showRequestForm'])->name('password.otp.request');
Route::post('/password/otp/send', [PasswordResetController::class, 'sendOtp'])->name('password.otp.send');

// Route untuk menampilkan form verifikasi OTP
Route::get('/password/otp/verify', [PasswordResetController::class, 'showVerifyForm'])->name('password.otp.verify');

// Route untuk memproses verifikasi OTP
Route::post('/password/otp/verify', [PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify.submit');

Route::get('/password/otp/reset', [PasswordResetController::class, 'showResetForm'])->name('password.otp.reset.form');
Route::post('/password/otp/reset', [PasswordResetController::class, 'resetPassword'])->name('password.otp.update');


Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

Route::get('/popular', [PageController::class, 'popular'])->name('popular');