# 📚 USER GUIDE APLIKASI KARSIS
*Sistem Manajemen Karya Siswa Digital*

---

## 🎯 TENTANG APLIKASI

**KARSIS (Karya Siswa)** adalah platform digital untuk mengelola dan mempublikasikan karya siswa dengan sistem moderasi berlapis. Aplikasi ini memungkinkan siswa mengupload karya mereka, guru/admin melakukan moderasi, dan komunitas sekolah berinteraksi melalui like dan komentar.

### Fitur Utama:
- ✅ Upload karya multi-format (gambar, video, dokumen, kode)
- ✅ Sistem moderasi draft → published
- ✅ Notifikasi email dan database real-time
- ✅ Dashboard statistik dengan charts
- ✅ Manajemen OSIS
- ✅ Interaksi sosial (like, comment)
- ✅ Multi-role authentication (Admin, Guru, Siswa, Guest)

---

## 👥 PANDUAN BERDASARKAN ROLE

### 🔴 ADMIN
**Akses Penuh Sistem**

#### Login:
- Email: admin@sekolah.id
- NIS: (jika ada)
- Mode: Internal Email/NIS

#### Fitur Admin:
1. **Dashboard Statistik** (`/dashboard/statistik`)
   - Lihat metrics: Draft, Published, Users, Comments
   - Charts analisis: Status, File Type, Content Type, Role
   - Export Excel/PDF

2. **Manajemen User** (`/admin`)
   - CRUD user (Create, Read, Update, Delete)
   - Import user via Excel
   - Assign role (admin, guru, siswa, guest)

3. **Moderasi Karya** (`/moderasi/drafts`)
   - Review draft siswa
   - Publish/Unpublish karya
   - Preview karya sebelum publikasi

4. **Upload Karya**
   - Upload langsung publish (bypass moderasi)
   - Semua jenis konten: karya, mading, harian, mingguan, opini, prestasi, event

5. **Manajemen OSIS** (`/admin/osis`)
   - Kelola anggota OSIS
   - Upload foto anggota
   - Atur struktur organisasi

### 🟡 GURU
**Moderator & Educator**

#### Login:
- Email: guru@sekolah.id
- NIS: (jika ada)
- Mode: Internal Email/NIS

#### Fitur Guru:
1. **Moderasi Karya**
   - Review dan approve draft siswa
   - Kirim feedback melalui sistem

2. **Upload Karya**
   - Upload langsung publish
   - Konten edukatif dan materi pembelajaran

3. **Manajemen OSIS**
   - Kelola kegiatan OSIS
   - Update informasi organisasi

### 🟢 SISWA
**Content Creator**

#### Login:
- NIS: 12345678
- Email: siswa@sekolah.id
- Mode: Internal NIS/Email

#### Fitur Siswa:
1. **Upload Karya** (`/upload`)
   - Upload file (max 500MB)
   - Tambah thumbnail (opsional, max 2MB)
   - Pilih kategori: karya, mading, opini, prestasi
   - Status otomatis: DRAFT

2. **Dashboard** (`/dashboard`)
   - Lihat karya sendiri
   - Status: Draft (menunggu moderasi) / Published

3. **Edit Karya** (`/works/{id}/edit`)
   - Edit judul, deskripsi
   - Ganti file/thumbnail
   - Hanya karya sendiri

4. **Interaksi Sosial**
   - Like karya teman
   - Komentar pada karya published
   - Notifikasi real-time

### 🔵 GUEST
**Visitor**

#### Login:
- Email: guest@example.com
- Mode: Guest

#### Fitur Guest:
- View karya published
- Profil terbatas
- Tidak bisa upload/interaksi

---

## 📤 CARA UPLOAD KARYA

### Langkah-langkah:

1. **Login** sebagai Siswa/Guru/Admin
2. **Klik "Upload"** di menu navigasi
3. **Isi Form Upload:**
   ```
   - Judul: [Nama karya Anda]
   - Deskripsi: [Penjelasan singkat]
   - File: [Pilih file utama]
   - Thumbnail: [Opsional - gambar preview]
   - Kategori: [karya/mading/opini/prestasi/event]
   ```
4. **Klik "Upload"**
5. **Status:**
   - Siswa: DRAFT (menunggu moderasi)
   - Guru/Admin: PUBLISHED (langsung tayang)

### File yang Didukung:
- **Gambar**: JPG, PNG, GIF (max 500MB)
- **Video**: MP4, MOV, AVI, MKV (max 500MB)
- **Audio**: MP3, WAV (max 500MB)
- **Dokumen**: PDF, DOC, DOCX, PPT, XLS (max 500MB)
- **Kode**: PY, JS, HTML, CSS, PHP, JAVA (max 500MB)
- **Arsip**: ZIP, RAR (max 500MB)
- **Executable**: EXE, APK (max 500MB)

---

## 🔍 SISTEM MODERASI

### Workflow Moderasi:

```
SISWA UPLOAD → DRAFT → REVIEW GURU/ADMIN → PUBLISHED
```

### Proses Detail:

1. **Siswa Upload Karya**
   - Status: DRAFT
   - Email otomatis ke Admin/Guru
   - Notifikasi database

2. **Admin/Guru Review** (`/moderasi/drafts`)
   - Lihat daftar draft
   - Preview karya
   - Keputusan: Publish/Reject

3. **Publikasi**
   - Status: PUBLISHED
   - Email ke siswa pemilik
   - Notifikasi database
   - Tampil di halaman publik

4. **Unpublish** (jika perlu)
   - Kembali ke status DRAFT
   - Notifikasi ke pemilik

---

## 🔔 SISTEM NOTIFIKASI

### Email Notifications:
- **Draft Submitted**: Ke Admin/Guru saat siswa upload
- **Work Published**: Ke siswa saat karya dipublikasi
- **Password Reset**: OTP via email/SMS

### Database Notifications:
- Real-time notification panel
- Unread count badge
- Mark as read functionality
- URL redirect ke konten terkait

---

## 📊 DASHBOARD STATISTIK

### Metrics Utama:
- **Draft Count**: Karya menunggu moderasi
- **Published Count**: Karya yang sudah dipublikasi  
- **Total Users**: Jumlah pengguna aktif
- **Total Comments**: Interaksi komunitas

### Charts Analytics:
1. **Status Chart**: Pie chart draft vs published
2. **File Type Chart**: Distribusi jenis file
3. **Content Type Chart**: Kategori konten
4. **Role Chart**: Pembagian user per role

### Export Features:
- **Excel**: Data artikel lengkap dengan filter
- **PDF**: Laporan statistik visual

---

## 🏢 MANAJEMEN OSIS

### Struktur Data:
- **Nama**: Nama anggota OSIS
- **Role**: Ketua/Sekretaris/Anggota
- **Type**: Inti/Sekbid
- **Nama Sekbid**: Nama seksi bidang
- **Angkatan**: Tahun kepengurusan
- **Foto**: Upload foto anggota
- **Order**: Urutan tampil

### Halaman Publik:
- URL: `/osis`
- Tampilan struktur organisasi
- Foto dan biodata anggota
- Responsive design

---

## 🔐 AUTENTIKASI & KEAMANAN

### Login Multi-Mode:
1. **Internal NIS**: Untuk siswa/guru/admin
2. **Internal Email**: Untuk guru/admin
3. **Guest Email**: Untuk tamu

### Password Reset:
1. Klik "Lupa Password"
2. Masukkan email/nomor HP
3. Terima OTP via SMS/Email
4. Verifikasi OTP
5. Set password baru

### Role-Based Access:
- Middleware `role:admin,guru,siswa`
- Route protection
- Feature-level permissions

---

## 💬 INTERAKSI SOSIAL

### Like System:
- Toggle like/unlike
- Real-time count update
- User tracking per karya

### Comment System:
- CRUD comments
- Real-time comment count
- Author dapat hapus comment
- Nested reply (future)

---

## 🔍 PENCARIAN

### Search Features:
- **Global Search**: Judul, deskripsi, author
- **User Search**: Nama, email, NIS
- **Filter by Type**: Kategori konten
- **Status Filter**: Draft/Published

### Search Endpoints:
- `/search/results?q={query}`
- `/search/users?q={query}`
- `/search/all`

---

## 📱 RESPONSIVE DESIGN

### Mobile Features:
- Bottom navigation bar
- Touch-friendly interface
- Responsive charts
- Mobile-optimized upload
- Swipe gestures

### Desktop Features:
- Full dashboard layout
- Advanced charts
- Bulk operations
- Keyboard shortcuts

---

## ⚠️ TROUBLESHOOTING

### Upload Gagal:
- Cek ukuran file (max 500MB)
- Pastikan format file didukung
- Refresh halaman dan coba lagi

### Email Tidak Terkirim:
- Cek folder spam
- Verifikasi email address
- Hubungi admin sistem

### Notifikasi Tidak Muncul:
- Refresh halaman
- Clear browser cache
- Cek koneksi internet

### Login Bermasalah:
- Pastikan NIS/email benar
- Cek mode login yang dipilih
- Gunakan fitur reset password

---

## 📞 KONTAK SUPPORT

- **Admin Sistem**: admin@sekolah.id
- **IT Support**: it@sekolah.id
- **Telepon**: (021) 1234-5678
- **WhatsApp**: +62 812-3456-7890

---

## 📋 FAQ

**Q: Berapa lama moderasi karya?**
A: Maksimal 2x24 jam kerja

**Q: Bisakah edit karya yang sudah published?**
A: Ya, pemilik dan admin bisa edit

**Q: Format file apa saja yang didukung?**
A: Gambar, video, audio, dokumen, kode, arsip, executable

**Q: Bagaimana cara mengganti password?**
A: Gunakan fitur "Lupa Password" dengan OTP

**Q: Bisakah siswa langsung publish?**
A: Tidak, harus melalui moderasi guru/admin

---

*User Guide ini akan terus diperbarui sesuai perkembangan aplikasi*