<?php

namespace App\Exports;

use App\Models\Work;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArticlesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function collection()
    {
        // Ambil data dari database
        $works = Work::select('id', 'title', 'content_type', 'file_type', 'status', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Map ulang agar hanya kolom yang diinginkan yang muncul
        // Kita tidak menyertakan thumbnail_path atau thumbnail_url di sini
        return $works->map(function ($work) {
            // Gunakan accessor getTypeLabelAttribute untuk kolom "Jenis Artikel"
            return [
                'id' => $work->id,
                'title' => $work->title,
                'content_type' => $work->content_type,
                'file_type' => $work->file_type,
                'status' => $work->status,
                'type' => $work->type,
                'created_at' => $work->created_at,
                'jenis_artikel' => $work->type_label, // Gunakan accessor untuk label
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Jenis Konten',
            'Jenis File',
            'Status',
            'Tipe',
            'Tanggal Dibuat',
            'Jenis Artikel',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header: tebal, latar belakang biru, teks putih, rata tengah
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '1F4E79'], // Biru tua
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Format kolom "Tanggal Dibuat" (kolom G) ke format Indonesia
        $sheet->getStyle('G2:G' . $sheet->getHighestRow())->applyFromArray([
            'numberFormat' => [
                'code' => 'DD MMM YYYY HH:MM',
            ],
        ]);

        // Rata kiri untuk: Judul, Jenis Konten, Jenis File, Jenis Artikel
        $sheet->getStyle('B2:B' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('C2:C' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('D2:D' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('H2:H' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Rata tengah untuk: ID, Status, Tipe
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('E2:E' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('F2:F' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Tambahkan border ke seluruh data (A1 sampai H terakhir)
        $sheet->getStyle('A1:H' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 40,  // Judul
            'C' => 15,  // Jenis Konten
            'D' => 12,  // Jenis File
            'E' => 10,  // Status
            'F' => 15,  // Tipe
            'G' => 20,  // Tanggal Dibuat
            'H' => 15,  // Jenis Artikel
        ];
    }
}