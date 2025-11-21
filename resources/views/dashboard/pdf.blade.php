<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Artikel</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        /* Gaya untuk Kesimpulan dan Diagram */
        .conclusion-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .conclusion-section h3 {
            margin-top: 0;
            color: #007bff;
        }
        .chart-container {
            text-align: center;
            margin: 15px 0;
        }
        .chart-image {
            width: 200px; /* Atur ukuran sesuai kebutuhan */
            height: 200px; /* Atur ukuran sesuai kebutuhan */
        }
        .chart-caption {
            font-style: italic;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>
    <h2>Laporan Artikel</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>File Type</th>
                <th>Content Type</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Pembuat</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $index => $article)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $article->title }}</td>
                    <td>{{ $article->file_type }}</td>
                    <td>{{ $article->content_type }}</td>
                    <td>{{ $article->type_label }}</td>
                    <td>{{ ucfirst($article->status) }}</td>
                    <td>{{ $article->user->name ?? 'N/A' }}</td>
                    <td>{{ $article->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="conclusion-section">
        <h3>Kesimpulan dan Distribusi Status Artikel</h3>
        <p>Berdasarkan data keseluruhan artikel, distribusi status artikel adalah sebagai berikut:</p>

        <!-- Diagram Lingkaran untuk Status Artikel -->
        <div class="chart-container">
            <!-- Gantilah placeholder ini dengan SVG atau gambar dari server-side chart kamu -->
            <!-- Contoh: -->
            <img src="data:image/svg+xml;base64,{{ base64_encode(generatePieChartSVG($draftCount, $publishedCount, ['Draft', 'Published'], ['#ffc107', '#198754'])) }}" class="chart-image" alt="Distribusi Status Artikel">
            <div class="chart-caption">Distribusi Status Artikel (Draft vs Published)</div>
        </div>

        <!-- Teks Kesimpulan -->
        <p>
            <strong>Total Artikel:</strong> {{ $draftCount + $publishedCount }}<br>
            <strong>Artikel Draft:</strong> {{ $draftCount }} ({{ $draftCount + $publishedCount > 0 ? number_format(($draftCount / ($draftCount + $publishedCount)) * 100, 2) : 0 }}%)<br>
            <strong>Artikel Published:</strong> {{ $publishedCount }} ({{ $draftCount + $publishedCount > 0 ? number_format(($publishedCount / ($draftCount + $publishedCount)) * 100, 2) : 0 }}%)
        </p>
        <p>Sebagian besar artikel berstatus ... (Tambahkan analisis singkat kamu di sini berdasarkan persentase).</p>
    </div>
</body>
</html>

<?php
// Fungsi bantu untuk membuat diagram lingkaran sederhana dalam format SVG
// Kode ini hanya untuk rendering PDF, bukan untuk ditampilkan di browser langsung.
// Catatan: Ini adalah implementasi dasar dan mungkin perlu disesuaikan lebih lanjut untuk kebutuhan visual yang lebih kompleks.

function generatePieChartSVG($value1, $value2, $labels, $colors) {
    $total = $value1 + $value2;
    if ($total == 0) {
        // Kembalikan SVG kosong atau lingkaran abu-abu jika tidak ada data
        return '<svg width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#ccc"/></svg>';
    }

    $angle1 = ($value1 / $total) * 360;
    $angle2 = ($value2 / $total) * 360;

    $x1 = 50 + 40 * cos(deg2rad($angle1 - 90));
    $y1 = 50 + 40 * sin(deg2rad($angle1 - 90));

    $largeArcFlag = $angle1 > 180 ? 1 : 0;

    $svg = '<svg width="100" height="100" viewBox="0 0 100 100">';
    // Gambar lingkaran latar belakang untuk slice kedua
    $svg .= '<circle cx="50" cy="50" r="40" fill="'.$colors[1].'" />';
    // Gambar slice pertama
    $svg .= '<path d="M 50 50 L 50 10 A 40 40 0 '.$largeArcFlag.' 1 '.$x1.' '.$y1.' L 50 50" fill="'.$colors[0].'" />';
    $svg .= '</svg>';

    return $svg;
}
?>