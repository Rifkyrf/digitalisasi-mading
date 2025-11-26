# 🎯 CATATAN PRESENTASI KARSIS
*Langkah-langkah Pembuatan Sistem Manajemen Karya Siswa (Berdasarkan Kode Aktual)*

---

## 📋 SLIDE 1: PEMBUKAAN
### Judul: "KARSIS - Sistem Manajemen Karya Siswa Digital"
- **Nama Aplikasi**: KARSIS (Karya Siswa)
- **Tujuan**: Platform digital untuk mengelola karya siswa dengan moderasi
- **Target User**: Admin, Guru, Siswa, Guest
- **Tech Stack**: Laravel 11 + MySQL + Bootstrap 5 + Chart.js
- **Database**: 24 tabel dengan relasi kompleks

---

## 📋 SLIDE 2: ANALISIS KODE EXISTING
### Struktur Project yang Sudah Ada:
```
app/Models/: User.php, Work.php, Comment.php, Like.php, Hakguna.php
app/Controllers/: WorkController.php, AuthController.php, DashboardController.php
resources/views/: dashboard/, works/, auth/, moderasi/
database/migrations/: 24 migration files
routes/web.php: 50+ routes dengan middleware
```

### Fitur yang Sudah Terimplementasi:
- ✅ Multi-role authentication dengan hakguna table
- ✅ Upload system dengan 15+ format file
- ✅ Workflow moderasi draft → published
- ✅ Dashboard statistik dengan Chart.js
- ✅ Email notifications (DraftSubmitted, WorkPublished)
- ✅ OSIS management system

---

## 📋 SLIDE 3: TAHAP 1 - DATABASE DESIGN
### Migration Files (Berdasarkan Kode Aktual):
```sql
-- 1. Core Tables (2025-08-21)
CREATE TABLE works (
    id bigint PRIMARY KEY,
    title varchar(255) NOT NULL,
    description text,
    file_path varchar(255),
    file_type varchar(255),
    content_type varchar(255),
    user_id bigint,
    thumbnail_path varchar(255),
    type enum('karya','mading','harian','mingguan','opini','prestasi','event'),
    status enum('draft','published') DEFAULT 'draft'
);

-- 2. Role System (2025-11-21)
CREATE TABLE hakgunas (
    id bigint PRIMARY KEY,
    name varchar(255) -- admin, guru, siswa, guest
);

-- 3. Social Features (2025-09-19)
CREATE TABLE comments (
    id bigint PRIMARY KEY,
    user_id bigint,
    work_id bigint,
    content text NOT NULL
);

CREATE TABLE likes (
    id bigint PRIMARY KEY,
    user_id bigint,
    work_id bigint
);
```

---

## 📋 SLIDE 4: TAHAP 2 - MODEL RELATIONSHIPS
### User Model (app/Models/User.php):
```php
class User extends Authenticatable {
    protected $fillable = ['name', 'email', 'password', 'nis', 'role', 'profile_photo', 'bio'];
    
    // Relasi ke Hakguna (Role System)
    public function hakguna() {
        return $this->belongsTo(Hakguna::class, 'role');
    }
    
    // Relasi ke Works
    public function works() {
        return $this->hasMany(Work::class);
    }
    
    // Helper Methods
    public function isAdmin() { return $this->hakguna && $this->hakguna->name === 'admin'; }
    public function isGuru() { return $this->hakguna && $this->hakguna->name === 'guru'; }
    public function isSiswa() { return $this->hakguna && $this->hakguna->name === 'siswa'; }
}
```

### Work Model (app/Models/Work.php):
```php
class Work extends Model {
    protected $fillable = ['title', 'description', 'file_path', 'file_type', 
                          'content_type', 'user_id', 'thumbnail_path', 'type', 'status'];
    
    // Scopes untuk filtering
    public function scopePublished($query) { return $query->where('status', 'published'); }
    public function scopeDraft($query) { return $query->where('status', 'draft'); }
    
    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function likes() { return $this->hasMany(Like::class); }
    
    // Accessors
    public function getFileUrlAttribute() { return asset('storage/' . $this->file_path); }
    public function getThumbnailUrlAttribute() { /* Logic untuk thumbnail */ }
}
```

---

## 📋 SLIDE 5: TAHAP 3 - AUTHENTICATION CONTROLLER
### AuthController.php (Multi-Mode Login):
```php
class AuthController extends Controller {
    public function login(Request $request) {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
            'login_as' => 'required|in:internal_nis,internal_email,guest',
        ]);
        
        $user = null;
        if ($loginAs === 'internal_nis') {
            $user = User::where('nis', $identifier)
                       ->whereHas('hakguna', function ($query) {
                           $query->whereIn('name', ['siswa', 'guru', 'admin']);
                       })->first();
        } elseif ($loginAs === 'internal_email') {
            $user = User::where('email', $identifier)
                       ->whereHas('hakguna', function ($query) {
                           $query->whereIn('name', ['siswa', 'guru', 'admin']);
                       })->first();
        } else {
            // Guest login logic
        }
        
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return redirect($user->isGuest() ? '/profile/' . $user->id : '/dashboard');
        }
    }
}
```

---

## 📋 SLIDE 6: TAHAP 4 - WORK CONTROLLER (Upload System)
### WorkController.php - Store Method (Kode Aktual):
```php
public function store(Request $request) {
    // Validasi komprehensif
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,mkv,mp3,wav,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,py,js,html,css,php,java,cpp,json,xml,yml,md,zip,rar,exe,apk|max:512000',
        'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'type' => ['required', Rule::in(['karya', 'mading', 'harian', 'mingguan', 'opini', 'prestasi', 'event'])],
    ]);
    
    // File processing
    $file = $request->file('file');
    $originalPath = $file->store('uploads', 'public');
    $extension = strtolower($file->getClientOriginalExtension());
    
    // Thumbnail handling
    $thumbnailPath = null;
    if ($request->hasFile('thumbnail')) {
        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
    }
    
    // Status logic: Admin langsung publish, Siswa ke draft
    $status = Auth::user()->isAdmin() ? 'published' : 'draft';
    
    $work = Work::create([
        'title' => $request->title,
        'description' => $request->description,
        'file_path' => $originalPath,
        'file_type' => $extension,
        'content_type' => $this->determineContentType($extension),
        'user_id' => Auth::id(),
        'thumbnail_path' => $thumbnailPath,
        'type' => $request->type,
        'status' => $status,
    ]);
    
    // Email notification untuk draft
    if ($work->status === 'draft') {
        $adminsAndGurus = User::whereHas('hakguna', function($q) {
            $q->whereIn('name', ['admin', 'guru']);
        })->get();
        
        foreach ($adminsAndGurus as $user) {
            Mail::to($user->email)->send(new DraftSubmitted($work));
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Draft Baru Dikirim',
                'message' => 'Siswa "'.$work->user->name.'" mengirim draft: "'.$work->title.'"',
                'type' => 'draft_submitted',
                'url' => route('moderator.show', $work),
            ]);
        }
    }
}
```

### Content Type Determination:
```php
private function determineContentType($extension) {
    $map = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'video' => ['mp4', 'webm', 'mov', 'avi', 'mkv', 'flv'],
        'document' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'],
        'code' => ['py', 'js', 'html', 'css', 'php', 'java', 'cpp', 'json', 'xml', 'yml', 'md'],
    ];
    
    foreach ($map as $type => $exts) {
        if (in_array($extension, $exts)) return $type;
    }
    return 'file';
}
```

---

## 📋 SLIDE 7: TAHAP 5 - MODERASI SYSTEM
### Publish Method (Kode Aktual):
```php
public function publish(Work $work) {
    if (!Auth::user()->isAdmin() && !Auth::user()->isGuru()) {
        abort(403);
    }
    
    if ($work->status !== 'draft') {
        return back()->with('error', 'Artikel ini sudah dipublikasikan.');
    }
    
    $work->update(['status' => 'published']);
    
    // Email ke penulis
    try {
        Mail::to($work->user->email)->send(new WorkPublished($work));
        Log::info('Email publikasi berhasil dikirim ke: ' . $work->user->name);
    } catch (\Exception $e) {
        Log::error('Gagal mengirim email publikasi: ' . $e->getMessage());
    }
    
    // Database notification
    Notification::create([
        'user_id' => $work->user_id,
        'title' => 'Karya Dipublikasikan',
        'message' => 'Karya Anda "'.$work->title.'" telah berhasil dipublikasikan.',
        'type' => 'work_published',
        'url' => route('work.show', $work),
    ]);
    
    return back()->with('success', 'Artikel berhasil dipublikasikan!');
}

public function drafts(Request $request) {
    $drafts = Work::draft()->with('user')->latest()->paginate(15);
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $drafts = Work::draft()->with('user')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            })->latest()->paginate(15);
    }
    
    return view('moderasi.drafts', compact('drafts'));
}
```

---

## 📋 SLIDE 8: TAHAP 6 - DASHBOARD CONTROLLER & STATISTICS
### DashboardController.php (Kode Aktual):
```php
class DashboardController extends Controller {
    public function index() {
        // Statistik utama
        $draftCount = Work::where('status', 'draft')->count();
        $publishedCount = Work::where('status', 'published')->count();
        $totalUsers = User::count();
        $totalComments = Comment::count();
        
        // Data untuk charts
        $statusData = [
            'draft' => $draftCount,
            'published' => $publishedCount
        ];
        
        $fileTypeData = Work::select('file_type', DB::raw('count(*) as count'))
                           ->groupBy('file_type')
                           ->pluck('count', 'file_type')
                           ->toArray();
        
        $contentTypeData = Work::select('content_type', DB::raw('count(*) as count'))
                              ->groupBy('content_type')
                              ->pluck('count', 'content_type')
                              ->toArray();
        
        $roleData = User::join('hakgunas', 'users.role', '=', 'hakgunas.id')
                       ->select('hakgunas.name', DB::raw('count(*) as count'))
                       ->groupBy('hakgunas.name')
                       ->pluck('count', 'name')
                       ->toArray();
        
        // Laporan artikel dengan search
        $query = Work::with('user');
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('file_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        $works = $query->latest()->paginate(15);
        
        return view('dashboard.statistik', compact(
            'draftCount', 'publishedCount', 'totalUsers', 'totalComments',
            'statusData', 'fileTypeData', 'contentTypeData', 'roleData', 'works'
        ));
    }
    
    public function exportExcel() {
        return Excel::download(new ArticlesExport, 'articles.xlsx');
    }
}
```

---

## 📋 SLIDE 9: TAHAP 7 - VIEW TEMPLATES (Frontend)
### Dashboard Statistik View (resources/views/dashboard/statistik.blade.php):
```html
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-stat-card stat-card-1">
                <div class="stat-card-content">
                    <span class="stat-number">{{ $draftCount }}</span>
                    <span class="stat-label">DRAFT</span>
                    <i class="fas fa-file-alt stat-icon"></i>
                </div>
            </div>
        </div>
        <!-- Repeat for other stats -->
    </div>
    
    <!-- Charts Section -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="chart-card card">
                <div class="card-header text-center">
                    <i class="fas fa-tasks me-2"></i>Status Karya
                </div>
                <div class="card-body p-0">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Repeat for other charts -->
    </div>
</div>

@push('scripts')
<script>
// Chart.js Implementation
const statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Draft', 'Published'],
        datasets: [{
            data: [{{ $draftCount }}, {{ $publishedCount }}],
            backgroundColor: ['#ffd700', '#28a745']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
@endsection
```

### Custom CSS (Kode Aktual dari File):
```css
.dashboard-stat-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    position: relative;
    min-height: 120px;
}

.dashboard-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card-2 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card-3 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stat-card-4 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
```

---

## 📋 SLIDE 10: TAHAP 8 - ROUTING & MIDDLEWARE
### Routes (web.php) - Kode Aktual:
```php
// Public routes
Route::get('/', [PageController::class, 'landing'])->name('home');
Route::get('/works/{id}', [WorkController::class, 'show'])->name('work.show');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [WorkController::class, 'index'])->name('dashboard');
    Route::delete('/works/{work}', [WorkController::class, 'destroy'])->name('work.destroy');
    Route::get('/works/{work}/edit', [WorkController::class, 'edit'])->name('works.edit');
    Route::put('/works/{work}', [WorkController::class, 'update'])->name('works.update');
});

// Role-based routes
Route::middleware(['auth','role:admin,guru,siswa'])->group(function () {
    Route::get('/upload', [WorkController::class, 'create'])->name('upload.page');
    Route::post('/upload', [WorkController::class, 'store'])->name('upload.store');
});

// Admin only routes
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/dashboard/statistik', [DashboardController::class, 'index'])->name('dashboard.statistik');
});

// Moderasi routes (Admin & Guru)
Route::middleware(['auth', 'role:admin,guru'])->prefix('moderasi')->group(function () {
    Route::get('/drafts', [WorkController::class, 'drafts'])->name('moderasi.drafts');
    Route::post('/{work}/publish', [WorkController::class, 'publish'])->name('moderasi.publish');
    Route::post('/{work}/unpublish', [WorkController::class, 'unpublish'])->name('moderasi.unpublish');
});
```

### Custom Middleware (role middleware):
```php
// app/Http/Middleware/RoleMiddleware.php
public function handle($request, Closure $next, ...$roles) {
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $userRole = Auth::user()->hakguna->name ?? null;
    
    if (!in_array($userRole, $roles)) {
        abort(403, 'Unauthorized access');
    }
    
    return $next($request);
}
```

---

## 📋 SLIDE 11: TAHAP 9 - EMAIL NOTIFICATIONS
### Mail Classes (Kode Aktual):
```php
// app/Mail/DraftSubmitted.php
class DraftSubmitted extends Mailable {
    public $work;
    
    public function __construct(Work $work) {
        $this->work = $work;
    }
    
    public function build() {
        return $this->subject('Draft Baru Dikirim - ' . $this->work->title)
                    ->view('mails.draft-submitted')
                    ->with([
                        'workTitle' => $this->work->title,
                        'authorName' => $this->work->user->name,
                        'workUrl' => route('moderator.show', $this->work)
                    ]);
    }
}

// app/Mail/WorkPublished.php
class WorkPublished extends Mailable {
    public $work;
    
    public function __construct(Work $work) {
        $this->work = $work;
    }
    
    public function build() {
        return $this->subject('Karya Anda Telah Dipublikasikan - ' . $this->work->title)
                    ->view('mails.work-published')
                    ->with([
                        'workTitle' => $this->work->title,
                        'workUrl' => route('work.show', $this->work)
                    ]);
    }
}
```

### Email Templates:
```html
<!-- resources/views/mails/draft-submitted.blade.php -->
<h2>Draft Baru Dikirim</h2>
<p>Siswa <strong>{{ $authorName }}</strong> telah mengirim draft baru:</p>
<h3>{{ $workTitle }}</h3>
<a href="{{ $workUrl }}" class="btn btn-primary">Review Draft</a>

<!-- resources/views/mails/work-published.blade.php -->
<h2>Karya Anda Telah Dipublikasikan!</h2>
<p>Selamat! Karya Anda <strong>{{ $workTitle }}</strong> telah berhasil dipublikasikan.</p>
<a href="{{ $workUrl }}" class="btn btn-success">Lihat Karya</a>
```

---

## 📋 SLIDE 12: TAHAP 10 - TESTING & DEBUGGING
### Testing Strategy (Berdasarkan Kode):
```php
// tests/Feature/WorkUploadTest.php
class WorkUploadTest extends TestCase {
    public function test_siswa_can_upload_work_as_draft() {
        $siswa = User::factory()->create(['role' => 3]); // role siswa
        
        $response = $this->actingAs($siswa)
            ->post('/upload', [
                'title' => 'Test Karya',
                'description' => 'Deskripsi test',
                'file' => UploadedFile::fake()->image('test.jpg'),
                'type' => 'karya'
            ]);
        
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('works', [
            'title' => 'Test Karya',
            'status' => 'draft',
            'user_id' => $siswa->id
        ]);
    }
    
    public function test_admin_can_upload_work_as_published() {
        $admin = User::factory()->create(['role' => 1]); // role admin
        
        $response = $this->actingAs($admin)
            ->post('/upload', [
                'title' => 'Admin Karya',
                'file' => UploadedFile::fake()->image('admin.jpg'),
                'type' => 'karya'
            ]);
        
        $this->assertDatabaseHas('works', [
            'title' => 'Admin Karya',
            'status' => 'published'
        ]);
    }
    
    public function test_guru_can_publish_draft() {
        $guru = User::factory()->create(['role' => 2]);
        $work = Work::factory()->create(['status' => 'draft']);
        
        $response = $this->actingAs($guru)
            ->post("/moderasi/{$work->id}/publish");
        
        $response->assertRedirect();
        $this->assertEquals('published', $work->fresh()->status);
    }
}
```

### Error Handling & Logging:
```php
// Dalam WorkController::store()
Log::info('Karya diunggah', [
    'work_id' => $work->id,
    'user_id' => Auth::id(),
    'title' => $work->title,
    'status' => $work->status,
]);

try {
    Mail::to($user->email)->send(new DraftSubmitted($work));
    Log::info('Email draft berhasil dikirim ke: ' . $user->name);
} catch (\Exception $e) {
    Log::error('Gagal mengirim email draft ke ' . $user->name . ': ' . $e->getMessage());
}
```

---

## 📋 SLIDE 13: DEPLOYMENT & PRODUCTION
### Environment Setup (.env):
```env
APP_NAME="KARSIS"
APP_ENV=production
APP_KEY=base64:generated_key
APP_DEBUG=false
APP_URL=https://karsis.sekolah.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=karsis
DB_USERNAME=karsis_user
DB_PASSWORD=secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=karsis@sekolah.id
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls

TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=+1234567890
```

### Deployment Commands:
```bash
# Clone repository
git clone https://github.com/sekolah/karsis.git
cd karsis

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --force
php artisan db:seed --class=UserSeeder

# Storage setup
php artisan storage:link
chmod -R 755 storage bootstrap/cache

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue worker (for email)
php artisan queue:work --daemon
```

---

## 📋 SLIDE 14: HASIL IMPLEMENTASI & METRICS
### Database Statistics (Berdasarkan Kode Aktual):
```sql
-- Query untuk mendapatkan statistik real
SELECT 
    (SELECT COUNT(*) FROM works WHERE status = 'draft') as draft_count,
    (SELECT COUNT(*) FROM works WHERE status = 'published') as published_count,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM comments) as total_comments,
    (SELECT COUNT(*) FROM likes) as total_likes;

-- Distribusi user per role
SELECT h.name as role, COUNT(u.id) as count 
FROM users u 
JOIN hakgunas h ON u.role = h.id 
GROUP BY h.name;

-- File types yang paling banyak diupload
SELECT file_type, COUNT(*) as count 
FROM works 
GROUP BY file_type 
ORDER BY count DESC;
```

### Performance Metrics (Implementasi Aktual):
- 📊 **Dashboard Load**: Chart.js rendering 4 charts simultaneously
- 📁 **File Upload**: Support hingga 500MB dengan progress indicator
- 🔍 **Search**: Real-time search dengan LIKE queries
- 📧 **Email Queue**: Background processing untuk notifications
- 📱 **Responsive**: Bootstrap 5 dengan custom CSS animations

### Security Features (Terimplementasi):
- 🔐 **CSRF Protection**: Laravel default middleware
- 🛡️ **File Validation**: Comprehensive MIME type checking
- 👤 **Role-based Access**: Custom middleware dengan hakguna table
- 🔒 **Password Hashing**: Laravel Hash facade
- 📧 **OTP Reset**: SMS/Email verification system

---

## 📋 SLIDE 15: DEMO APLIKASI (Berdasarkan Fitur Aktual)
### Demo Scenario 1: Siswa Upload Karya
```
1. Login dengan NIS: 12345678
2. Klik "Upload" di navbar
3. Isi form:
   - Judul: "Proyek Arduino LED Matrix"
   - Deskripsi: "Membuat display LED dengan Arduino"
   - File: arduino_project.zip (50MB)
   - Thumbnail: led_matrix.jpg
   - Kategori: "karya"
4. Submit → Status: DRAFT
5. Email otomatis ke admin/guru
6. Notifikasi database tersimpan
```

### Demo Scenario 2: Guru Moderasi
```
1. Login sebagai guru
2. Akses /moderasi/drafts
3. Lihat daftar draft siswa
4. Klik "Preview" untuk review
5. Klik "Publish" jika approved
6. Email otomatis ke siswa pemilik
7. Status berubah ke PUBLISHED
8. Karya muncul di halaman publik
```

### Demo Scenario 3: Dashboard Analytics
```
1. Login sebagai admin
2. Akses /dashboard/statistik
3. Lihat 4 metric cards:
   - Draft Count: 25
   - Published Count: 150
   - Total Users: 200
   - Total Comments: 500
4. View 4 interactive charts (Chart.js)
5. Export Excel report
6. Search & filter artikel
```

### Demo Scenario 4: Interaksi Sosial
```
1. Buka karya published
2. Klik tombol like (AJAX)
3. Tambah komentar
4. Real-time update counter
5. Notifikasi ke pemilik karya
```

---

## 📋 SLIDE 16: CHALLENGES & SOLUTIONS (Berdasarkan Pengalaman)
### Technical Challenges yang Dihadapi:

#### 1. File Upload Besar (500MB)
**Problem**: Default PHP upload limit
```php
// Solution: php.ini configuration
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
memory_limit = 512M

// Laravel validation
'file' => 'required|file|max:512000' // 500MB in KB
```

#### 2. Multi-Role Authentication
**Problem**: Complex login logic dengan 3 mode
```php
// Solution: Conditional user lookup
if ($loginAs === 'internal_nis') {
    $user = User::where('nis', $identifier)
               ->whereHas('hakguna', function ($query) {
                   $query->whereIn('name', ['siswa', 'guru', 'admin']);
               })->first();
}
```

#### 3. Email Notification Reliability
**Problem**: Email delivery failures
```php
// Solution: Try-catch dengan logging
try {
    Mail::to($user->email)->send(new DraftSubmitted($work));
    Log::info('Email berhasil dikirim');
} catch (\Exception $e) {
    Log::error('Email gagal: ' . $e->getMessage());
    // Fallback: Save to database notifications
}
```

#### 4. Database Performance
**Problem**: N+1 queries pada dashboard
```php
// Solution: Eager loading
$works = Work::with('user', 'comments', 'likes')->latest()->paginate(15);
$drafts = Work::draft()->with('user')->latest()->paginate(15);
```

### Lessons Learned:
- **Logging is crucial**: Setiap action penting harus di-log
- **Validation everywhere**: Client-side dan server-side
- **Graceful error handling**: User tidak boleh lihat error mentah
- **Database indexing**: Untuk query yang sering digunakan

---

## 📋 SLIDE 17: FUTURE ENHANCEMENTS (Berdasarkan Kode Base)
### Immediate Improvements (Next Sprint):
```php
// 1. Real-time notifications dengan WebSocket
use Pusher\Pusher;

public function publish(Work $work) {
    $work->update(['status' => 'published']);
    
    // Existing email logic...
    
    // New: Real-time notification
    $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
    $pusher->trigger('user-' . $work->user_id, 'work-published', [
        'message' => 'Karya Anda telah dipublikasikan!',
        'work_title' => $work->title
    ]);
}

// 2. Advanced search dengan Elasticsearch
public function search(Request $request) {
    $query = Work::search($request->q)
                 ->where('status', 'published');
    
    if ($request->type) {
        $query->where('type', $request->type);
    }
    
    return $query->paginate(15);
}

// 3. Bulk operations untuk admin
public function bulkPublish(Request $request) {
    $workIds = $request->work_ids;
    Work::whereIn('id', $workIds)->update(['status' => 'published']);
    
    // Send bulk notifications
    foreach ($workIds as $workId) {
        $work = Work::find($workId);
        Mail::to($work->user->email)->send(new WorkPublished($work));
    }
}
```

### Medium-term Enhancements:
- **API Development**: RESTful API untuk mobile app
- **Caching Layer**: Redis untuk dashboard statistics
- **File Optimization**: Image compression dan CDN
- **Advanced Analytics**: User behavior tracking

### Long-term Vision:
- **Multi-tenant**: Support multiple schools
- **AI Integration**: Content moderation dan recommendation
- **Mobile App**: React Native atau Flutter
- **Integration**: LMS dan sistem sekolah lainnya

---

## 📋 SLIDE 18: KESIMPULAN & TECHNICAL ACHIEVEMENTS
### Pencapaian Teknis:
✅ **24 Database Tables** dengan relasi yang solid
✅ **50+ Routes** dengan proper middleware protection
✅ **15+ File Formats** supported dengan validation
✅ **4-Role System** (Admin, Guru, Siswa, Guest)
✅ **Email Notifications** dengan queue system
✅ **Dashboard Analytics** dengan Chart.js
✅ **Responsive Design** Bootstrap 5 + custom CSS
✅ **Search Functionality** dengan pagination
✅ **File Storage** dengan thumbnail generation

### Code Quality Metrics:
```php
// Lines of Code (Estimated)
Models: ~500 lines
Controllers: ~1,500 lines
Views: ~2,000 lines
Migrations: ~800 lines
Routes: ~200 lines
Total: ~5,000 lines of PHP/Blade code

// Database Tables: 24
// Relationships: 15+
// Middleware: 3 custom
// Mail Classes: 4
// Seeders: 3
```

### Performance Achievements:
- 📊 **Dashboard**: 4 real-time charts
- 📁 **Upload**: 500MB file support
- 🔍 **Search**: Multi-field search dengan LIKE queries
- 📧 **Email**: Background queue processing
- 🔐 **Security**: Role-based access control

### Key Technical Decisions:
1. **Laravel 11**: Modern PHP framework dengan ecosystem lengkap
2. **MySQL**: Relational database untuk data consistency
3. **Bootstrap 5**: Responsive framework dengan customization
4. **Chart.js**: Interactive charts untuk analytics
5. **Local Storage**: Simple file management untuk MVP
6. **SMTP + Twilio**: Reliable notification delivery

---

## 📋 SLIDE 19: Q&A SESSION (Technical Focus)
### Pertanyaan Teknis yang Mungkin Diajukan:

**Q: Mengapa pilih Laravel dibanding framework lain?**
A: 
```php
// Laravel advantages untuk project ini:
- Built-in authentication & authorization
- Eloquent ORM untuk database relationships
- Mail system dengan queue support
- Blade templating yang powerful
- Artisan commands untuk development
- Middleware system untuk role-based access
```

**Q: Bagaimana handle file upload 500MB?**
A:
```php
// Multi-step approach:
1. PHP.ini configuration (upload_max_filesize, post_max_size)
2. Laravel validation dengan max:512000
3. Storage::disk('public') untuk file management
4. Progress indicator di frontend
5. Error handling dengan try-catch
```

**Q: Database performance dengan banyak data?**
A:
```sql
-- Optimization strategies:
1. Indexing pada foreign keys dan search fields
2. Eager loading untuk menghindari N+1 queries
3. Pagination untuk large datasets
4. Database query optimization

CREATE INDEX idx_works_status ON works(status);
CREATE INDEX idx_works_user_id ON works(user_id);
CREATE INDEX idx_comments_work_id ON comments(work_id);
```

**Q: Security measures yang diimplementasi?**
A:
```php
// Security layers:
1. CSRF protection (Laravel default)
2. SQL injection prevention (Eloquent ORM)
3. File validation (MIME type checking)
4. Role-based access control
5. Password hashing (Laravel Hash)
6. Input sanitization
7. XSS protection (Blade templating)
```

---

## 📋 SLIDE 20: DEMO PREPARATION & TIPS
### Pre-Demo Checklist:
```bash
# 1. Database seeding
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=WorkSeeder

# 2. Test accounts
Admin: admin@sekolah.id / password123
Guru: guru@sekolah.id / password123
Siswa: NIS 12345678 / password123

# 3. Sample files ready
- test_image.jpg (5MB)
- test_document.pdf (10MB)
- test_code.py (1MB)
- test_video.mp4 (50MB)

# 4. Clear cache
php artisan cache:clear
php artisan view:clear

# 5. Check storage permissions
chmod -R 755 storage/
```

### Live Demo Script:
```
1. Landing page → Show public works
2. Login siswa → Upload new work → Show draft status
3. Login guru → Moderasi drafts → Publish work
4. Show email notifications (Gmail/Mailtrap)
5. Login admin → Dashboard statistik → Show charts
6. Export Excel report
7. Show responsive design (mobile view)
8. Demonstrate search functionality
9. Show OSIS management
10. Like/comment interaction
```

### Backup Plan:
- Screenshot slideshow jika demo gagal
- Video recording dari successful demo
- Postman collection untuk API testing
- Database dump dengan sample data

---

## 🎯 PRESENTATION TIPS:
1. **Code Focus**: Tunjukkan kode aktual, bukan pseudocode
2. **Database First**: Mulai dari ERD dan migration files
3. **Progressive Build**: Tunjukkan evolution dari simple ke complex
4. **Error Handling**: Tunjukkan bagaimana handle edge cases
5. **Performance**: Diskusikan optimization yang dilakukan
6. **Security**: Highlight security measures yang diimplementasi
7. **Scalability**: Jelaskan bagaimana sistem bisa di-scale

---

*Catatan: Presentasi ini fokus pada implementasi teknis berdasarkan kode aktual yang ada*