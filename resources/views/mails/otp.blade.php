<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
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
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #c0d9ec;">Permintaan Reset Kata Sandi</p>
                    </td>
                </tr>

                <!-- Konten Utama -->
                <tr>
                    <td style="padding: 30px 40px 20px 40px;">

                        <!-- Judul Notifikasi -->
                        <h2 style="color: #003366; font-size: 20px; margin-top: 0; border-bottom: 2px solid #eef2f5; padding-bottom: 15px;">
                            🔐 Kode Verifikasi Satu Kali (OTP)
                        </h2>

                        <p style="margin: 0 0 16px 0;">Halo,</p>

                        <p style="margin: 0 0 24px 0;">Anda telah meminta untuk mereset kata sandi. Silakan gunakan kode verifikasi di bawah ini:</p>

                        <!-- Box Kode OTP (Paling Menonjol) -->
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse: collapse; background-color: #f6f9fc; border-radius: 8px; margin-bottom: 24px; border: 1px solid #dbe8f5;">
                            <tr>
                                <td align="center" style="padding: 25px 20px; font-size: 15px;">
                                    <p style="margin: 0 0 8px 0; color: #666666; font-weight: 400; font-size: 14px;">Kode OTP Anda:</p>

                                    <!-- Kode OTP -->
                                    <strong style="font-size: 32px;
                                                   letter-spacing: 5px;
                                                   color: #0056b3; /* Warna biru lebih terang */
                                                   padding: 8px 15px;
                                                   display: inline-block;
                                                   border: 2px dashed #dbe8f5;
                                                   background-color: #ffffff;
                                                   border-radius: 6px;">
                                        {{ $otp }}
                                    </strong>
                                    <!-- Akhir Kode OTP -->
                                </td>
                            </tr>
                        </table>

                        <!-- Peringatan Batas Waktu -->
                        <p style="margin: 0 0 24px 0; text-align: center; color: #cc3300; font-weight: 600; font-size: 14px;">
                            ⚠️ Kode ini hanya berlaku selama 10 menit!
                        </p>

                        <p style="margin: 0;">
                            Jika Anda tidak meminta reset kata sandi, harap abaikan email ini. Keamanan akun Anda tetap terjaga.
                        </p>

                        <p style="margin: 24px 0 0 0; border-top: 1px solid #eef2f5; padding-top: 20px;">
                            Terima kasih atas perhatiannya.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 20px 40px; background-color: #eef2f5;">
                        <p style="margin: 0; font-size: 12px; color: #777777;">
                            Pemberitahuan keamanan ini dikirim secara otomatis oleh sistem
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