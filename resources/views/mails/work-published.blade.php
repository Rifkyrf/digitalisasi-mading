<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karya Anda Dipublikasikan!</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        /* Reset CSS dasar untuk kompatibilitas */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        /* Memastikan body menggunakan font Inter (atau fallback) */
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; line-height: 1.6; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; color: #333333;">

    <!-- Wrapper Utama (Seluruh Body) -->
    <center style="width: 100%; background-color: #f4f7f6;">
        <div style="max-width: 600px;">

            <!-- Struktur Konten Email -->
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; max-width: 600px; border-collapse: collapse; border-spacing: 0; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);" width="600">

                <!-- Header Biru Tua (Identitas Sekolah) -->
                <tr>
                    <td align="center" style="padding: 24px 20px; background-color: #003366; color: #ffffff;">
                        <h1 style="margin: 0; font-size: 24px; font-weight: 700; line-height: 1.2;">
                            SMK Bakti Nusantara 666
                        </h1>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #c0d9ec;">Sistem Informasi Akademik & Karya Siswa</p>
                    </td>
                </tr>

                <!-- Konten Utama -->
                <tr>
                    <td style="padding: 30px 40px 20px 40px;">

                        <!-- Judul Notifikasi (Hijau untuk Sukses) -->
                        <h2 style="color: #28a744; /* Warna hijau sukses */ font-size: 20px; margin-top: 0; border-bottom: 2px solid #eef2f5; padding-bottom: 15px;">
                            🎉 Karya Anda Dipublikasikan!
                        </h2>

                        <p style="margin: 0 0 16px 0;">Halo **{{ $author }}**,</p>

                        <p style="margin: 0 0 24px 0;">Selamat! Karya terbaik Anda telah **disetujui** dan **berhasil dipublikasikan** di mading digital sekolah.</p>

                        <!-- Box Detail Karya -->
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse: collapse; border: 1px solid #dbe8f5; background-color: #f6f9fc; border-radius: 8px; margin-bottom: 24px;">
                            <tr>
                                <td style="padding: 18px 20px; font-size: 15px;">
                                    <p style="margin: 0 0 8px 0; color: #666666; font-weight: 400;"><strong>Status:</strong> <span style="color: #28a744; font-weight: 600;">Dipublikasikan</span></p>
                                    <p style="margin: 0; color: #666666; font-weight: 400;"><strong>Judul Karya:</strong> <span style="color: #333333; font-weight: 700;">"{{ $work->title }}"</span></p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin: 0 0 24px 0;">Kontribusi Anda sangat dihargai. Kini karya Anda dapat dilihat oleh seluruh komunitas SMK Bakti Nusantara 666!</p>

                        <!-- Tombol CTA (Call to Action) - Hijau -->
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ route('work.show', $work) }}"
                                style="background-color: #28a744; /* Warna hijau untuk CTA Sukses */
                                        color: white;
                                        padding: 12px 25px;
                                        text-decoration: none;
                                        border-radius: 8px;
                                        display: inline-block;
                                        font-weight: 600;
                                        font-size: 16px;
                                        border: 1px solid #208736;"
                                target="_blank"
                            >
                                🚀 Lihat Karya Anda Sekarang
                            </a>
                        </div>

                        <p style="margin: 24px 0 0 0; border-top: 1px solid #eef2f5; padding-top: 20px;">
                            Terima kasih atas kontribusinya. Selamat berkarya lagi!
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 20px 40px; background-color: #eef2f5;">
                        <p style="margin: 0; font-size: 12px; color: #777777;">
                            Pemberitahuan ini dikirim secara otomatis oleh sistem
                        </p>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #555555; font-weight: 600;">
                            Sistem Informasi SMK Bakti Nusantara 666
                        </p>
                    </td>
                </tr>
            </table>
            <!-- Akhir Struktur Konten Email -->

        </div>
    </center>
    <!-- Akhir Wrapper Utama -->

</body>
</html>