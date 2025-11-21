<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Draft Baru</title>
    <!-- Penggunaan font yang aman untuk email. Poppins adalah font modern, tetapi fallback ke sans-serif sangat penting. -->
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

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

                        <!-- Judul Notifikasi -->
                        <h2 style="color: #003366; font-size: 20px; margin-top: 0; border-bottom: 2px solid #eef2f5; padding-bottom: 15px;">
                            🔔 Pemberitahuan Draft Baru
                        </h2>

                        <p style="margin: 0 0 16px 0;">Halo Admin/Guru,</p>

                        <p style="margin: 0 0 24px 0;">Draft baru telah berhasil dikirimkan oleh siswa untuk segera ditinjau:</p>

                        <!-- Box Detail Draft -->
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse: collapse; border: 1px solid #dbe8f5; background-color: #f6f9fc; border-radius: 8px; margin-bottom: 24px;">
                            <tr>
                                <td style="padding: 18px 20px; font-size: 15px;">
                                    <p style="margin: 0 0 8px 0; color: #666666; font-weight: 400;"><strong>Pengirim:</strong> <span style="color: #003366; font-weight: 600;">{{ $author }}</span></p>
                                    <p style="margin: 0 0 8px 0; color: #666666; font-weight: 400;"><strong>Judul Karya:</strong> <span style="color: #333333; font-weight: 600;">{{ $work->title }}</span></p>
                                    <p style="margin: 0 0 12px 0; color: #666666; font-weight: 400;"><strong>Status:</strong> <span style="color: #d97706; font-weight: 600;">Menunggu Verifikasi</span></p>
                                    <p style="margin: 0; color: #666666; font-weight: 400; border-top: 1px dashed #cccccc; padding-top: 12px;"><strong>Deskripsi Singkat:</strong> <br> <em style="color: #555555;">{{ $work->description }}</em></p>
                                </td>
                            </tr>
                        </table>

                        <!-- Tombol CTA (Call to Action) -->
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ route('moderator.show', $work) }}"
                                style="background-color: #0056b3; /* Biru terang untuk CTA */
                                        color: white;
                                        padding: 12px 25px;
                                        text-decoration: none;
                                        border-radius: 8px;
                                        display: inline-block;
                                        font-weight: 600;
                                        font-size: 16px;
                                        border: 1px solid #004499;"
                                target="_blank"
                            >
                                ➡️ Lihat & Tinjau Draft Sekarang
                            </a>
                        </div>

                        <p style="margin: 24px 0 0 0; border-top: 1px solid #eef2f5; padding-top: 20px;">
                            Terima kasih atas kerja sama dan waktu Anda dalam proses peninjauan ini.
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