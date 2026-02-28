<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

/*
 |──────────────────────────────────────────────────────────────
 |  WPOS · Toko Sahabat  —  Brand Color Reference (dari UI)
 |──────────────────────────────────────────────────────────────
 |  Sidebar / brand utama  : #15803D  (green-700)
 |  Kartu Total Penjualan  : #2563EB  (blue-600)
 |  Kartu Total Transaksi  : #16A34A  (green-600)
 |  Kartu Total Kembalian  : #9333EA  (purple-600)
 |  Aksen tombol Filter    : #2563EB  (blue-600)
 |──────────────────────────────────────────────────────────────
 */

// ═══════════════════════════════════════════════════════════════
//  SHEET 1 ·  RINGKASAN
// ═══════════════════════════════════════════════════════════════
class RingkasanSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function title(): string
    {
        return '📊 Ringkasan';
    }
    public function columnWidths(): array
    {
        return ['A' => 36, 'B' => 28, 'C' => 5];
    }

    public function array(): array
    {
        $d   = $this->data;
        $avg = $d['totalTransaksi'] > 0 ? round($d['totalPenjualan'] / $d['totalTransaksi']) : 0;

        return [
            /* 1  */
            ['WPOS · TOKO SAHABAT', '', ''],
            /* 2  */
            ['Laporan Penjualan', '', ''],
            /* 3  */
            ['', '', ''],
            /* 4  */
            ['Periode',  Carbon::parse($d['tanggalMulai'])->format('d M Y') . '  –  ' . Carbon::parse($d['tanggalSelesai'])->format('d M Y'), ''],
            /* 5  */
            ['Dicetak', now()->format('d M Y, H:i') . ' WIB', ''],
            /* 6  */
            ['', '', ''],
            /* 7  */
            ['RINGKASAN STATISTIK', '', ''],
            /* 8  */
            ['Keterangan', 'Nilai', ''],
            /* 9  */
            ['💰  Total Penjualan',          (float) $d['totalPenjualan'], ''],
            /* 10 */
            ['🛒  Total Transaksi',           (int)   $d['totalTransaksi'], ''],
            /* 11 */
            ['🔄  Total Kembalian',           (float) $d['totalKembalian'], ''],
            /* 12 */
            ['📈  Rata-rata / Transaksi',     (float) $avg, ''],
            /* 13 */
            ['', '', ''],
            /* 14 */
            ['KETERANGAN WARNA SHEET', '', ''],
            /* 15 */
            ['  🟦  Sheet Riwayat Transaksi', 'Data semua transaksi dalam periode', ''],
            /* 16 */
            ['  🟩  Sheet Produk Terlaris',   'Top 10 produk terjual terbanyak', ''],
            /* 17 */
            ['  🟪  Sheet Detail Produk',     'Detail transaksi per produk', ''],
            /* 18 */
            ['  🟩  Sheet Per Kasir',         'Rekap penjualan per kasir', ''],
        ];
    }

    public function styles(Worksheet $ws): void
    {
        // ── Banner utama ────────────────────────────────────────
        $ws->mergeCells('A1:C1');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 18, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(42);

        // ── Sub-title ───────────────────────────────────────────
        $ws->mergeCells('A2:C2');
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => false, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF166534']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(2)->setRowHeight(22);

        // ── Info periode & cetak ────────────────────────────────
        foreach ([4, 5] as $r) {
            $ws->getStyle("A{$r}")->applyFromArray([
                'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
            ]);
            $ws->getStyle("B{$r}")->applyFromArray([
                'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
            ]);
        }

        // ── Section header: Ringkasan ───────────────────────────
        $ws->mergeCells('A7:C7');
        $ws->getStyle('A7')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $ws->getRowDimension(7)->setRowHeight(22);

        // ── Header kolom (A:C agar kolom C tidak bolong) ────────
        $ws->getStyle('A8:C8')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF1D4ED8']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF2563EB']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        // FIX: Merge B:C pada baris header agar label "Nilai" lebih lebar
        $ws->mergeCells('B8:C8');
        $ws->getRowDimension(8)->setRowHeight(20);

        // ── Data rows kartu — merge B:C agar nilai tidak terpotong ─
        // Biru → penjualan (row 9)
        $ws->mergeCells('B9:C9');
        $ws->getStyle('A9:C9')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
        ]);
        $ws->getRowDimension(9)->setRowHeight(24);

        // Hijau → transaksi (row 10)
        $ws->mergeCells('B10:C10');
        $ws->getStyle('A10:C10')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
        ]);
        $ws->getRowDimension(10)->setRowHeight(24);

        // Ungu → kembalian (row 11)
        $ws->mergeCells('B11:C11');
        $ws->getStyle('A11:C11')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF9333EA']],
        ]);
        $ws->getRowDimension(11)->setRowHeight(24);

        // Teal → rata-rata (row 12)
        $ws->mergeCells('B12:C12');
        $ws->getStyle('A12:C12')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D9488']],
        ]);
        $ws->getRowDimension(12)->setRowHeight(24);

        // Alignment kanan untuk kolom nilai (B)
        foreach ([9, 10, 11, 12] as $r) {
            $ws->getStyle("B{$r}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Format angka
        $ws->getStyle('B9')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->getStyle('B11')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->getStyle('B12')->getNumberFormat()->setFormatCode('"Rp "#,##0');

        // Border kotak statistik — extend ke C
        $ws->getStyle('A8:C12')->applyFromArray([
            'borders' => [
                'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF2563EB']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['argb' => 'FFE2E8F0']],
            ],
        ]);

        // ── Section keterangan warna (row 14-18) ───────────────
        $ws->mergeCells('A14:C14');
        $ws->getStyle('A14')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $ws->getRowDimension(14)->setRowHeight(20);

        $keteranganBg = ['FF2563EB', 'FF16A34A', 'FF9333EA', 'FF15803D'];
        foreach ([15, 16, 17, 18] as $i => $r) {
            $ws->mergeCells("A{$r}:C{$r}");
            $ws->getStyle("A{$r}:C{$r}")->applyFromArray([
                'font'      => ['name' => 'Calibri', 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $keteranganBg[$i]]],
            ]);
            $ws->getRowDimension($r)->setRowHeight(18);
        }
    }
}

// ═══════════════════════════════════════════════════════════════
//  SHEET 2 ·  RIWAYAT TRANSAKSI
// ═══════════════════════════════════════════════════════════════
class TransaksiSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function title(): string
    {
        return '🧾 Riwayat Transaksi';
    }

    public function array(): array
    {
        $rows = [
            ['WPOS · TOKO SAHABAT  —  RIWAYAT TRANSAKSI'],
            [
                'Periode:',
                Carbon::parse($this->data['tanggalMulai'])->format('d M Y')
                    . '  –  '
                    . Carbon::parse($this->data['tanggalSelesai'])->format('d M Y'),
            ],
            [],
            ['No.', 'No. Transaksi', 'Tanggal & Waktu', 'Kasir', 'Total (Rp)', 'Dibayar (Rp)', 'Kembalian (Rp)'],
        ];

        $totalPembayaran = 0.0;
        $totalBayar      = 0.0;
        $totalKembalian  = 0.0;
        foreach ($this->data['transaksi'] as $i => $item) {
            $totalPembayaran += (float) $item->total_pembayaran;
            $totalBayar      += (float) $item->total_bayar;
            $totalKembalian  += (float) $item->kembalian_pembayaran;
            $rows[] = [
                $i + 1,
                '#' . str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT),
                Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i'),
                $item->kasir ?? 'Admin',
                (float) $item->total_pembayaran,
                (float) $item->total_bayar,
                (float) $item->kembalian_pembayaran,
            ];
        }

        $rows[] = ['', '', '', 'TOTAL', $totalPembayaran, $totalBayar, $totalKembalian];

        return $rows;
    }

    public function styles(Worksheet $ws): void
    {
        $last = $ws->getHighestRow();

        // Banner
        $ws->mergeCells('A1:G1');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(34);

        // Sub baris periode
        $ws->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
        ]);
        $ws->getStyle('B2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
        ]);

        // Header kolom (row 4)
        $ws->getStyle('A4:G4')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
        ]);
        $ws->getRowDimension(4)->setRowHeight(22);

        // FIX: Freeze pane agar header tetap terlihat saat scroll
        $ws->freezePane('A5');

        // FIX: Alignment kolom No. (A) ke tengah
        $ws->getStyle("A5:A{$last}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows (alternating)
        for ($r = 5; $r < $last; $r++) {
            $bg = $r % 2 === 0 ? 'FFDBEAFE' : 'FFEFF6FF';
            $ws->getStyle("A{$r}:G{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
            // Warna biru pada kolom No. Transaksi
            $ws->getStyle("B{$r}")->applyFromArray([
                'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF2563EB']],
            ]);
            // FIX: Alignment kanan untuk kolom angka (Total, Dibayar, Kembalian)
            $ws->getStyle("E{$r}:G{$r}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getRowDimension($r)->setRowHeight(18);
        }

        // Total row
        $ws->getStyle("A{$last}:G{$last}")->applyFromArray([
            'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF15803D']],
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF15803D']],
            ],
        ]);
        // FIX: Alignment kanan kolom angka pada baris TOTAL
        $ws->getStyle("E{$last}:G{$last}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $ws->getRowDimension($last)->setRowHeight(24);

        // Format angka
        $ws->getStyle("E5:G{$last}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

        // Outline
        $ws->getStyle("A4:G{$last}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF2563EB']]],
        ]);
    }
}

// ═══════════════════════════════════════════════════════════════
//  SHEET 3 ·  PRODUK TERLARIS
// ═══════════════════════════════════════════════════════════════
class ProdukTerlarisSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function title(): string
    {
        return '🏆 Produk Terlaris';
    }

    public function array(): array
    {
        $rows = [
            ['WPOS · TOKO SAHABAT  —  TOP 10 PRODUK TERLARIS'],
            [
                'Periode:',
                Carbon::parse($this->data['tanggalMulai'])->format('d M Y')
                    . '  –  '
                    . Carbon::parse($this->data['tanggalSelesai'])->format('d M Y'),
            ],
            [],
            ['Peringkat', 'Nama Produk', 'Total Terjual (Qty)', 'Total Penjualan (Rp)'],
        ];

        $totalQtyAll       = 0;
        $totalPenjualanAll = 0.0;
        foreach ($this->data['produkTerlaris'] as $i => $p) {
            $medal = match ($i) {
                0 => '🥇',
                1 => '🥈',
                2 => '🥉',
                default => '#' . ($i + 1)
            };
            $totalQtyAll       += (int)   $p->total_qty;
            $totalPenjualanAll += (float) $p->total_penjualan;
            $rows[] = [$medal . '  ' . ($i + 1), $p->nama_produk, (int) $p->total_qty, (float) $p->total_penjualan];
        }

        $rows[] = ['', 'TOTAL', $totalQtyAll, $totalPenjualanAll];

        return $rows;
    }

    public function styles(Worksheet $ws): void
    {
        $last = $ws->getHighestRow();

        // Banner
        $ws->mergeCells('A1:D1');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(34);

        $ws->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
        ]);

        // Header kolom (row 4) — warna hijau (sesuai kartu Transaksi)
        $ws->getStyle('A4:D4')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(4)->setRowHeight(22);

        // FIX: Freeze pane agar header tetap terlihat saat scroll
        $ws->freezePane('A5');

        // Top 3 rows - warna khusus medali
        $topColors = ['FFFBBF24', 'FFD1D5DB', 'FFCD7F32'];
        for ($r = 5; $r <= min(7, $last - 1); $r++) {
            $ws->getStyle("A{$r}:D{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FF1E293B']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $topColors[$r - 5]]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
            // FIX: Alignment kanan untuk kolom angka
            $ws->getStyle("C{$r}:D{$r}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getRowDimension($r)->setRowHeight(22);
        }

        // Sisa rows
        for ($r = 8; $r < $last; $r++) {
            $bg = $r % 2 === 0 ? 'FFDCFCE7' : 'FFF0FDF4';
            $ws->getStyle("A{$r}:D{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
            // FIX: Alignment kanan untuk kolom angka
            $ws->getStyle("C{$r}:D{$r}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getRowDimension($r)->setRowHeight(18);
        }

        // Total row
        $ws->getStyle("A{$last}:D{$last}")->applyFromArray([
            'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF15803D']]],
        ]);
        // FIX: Alignment kanan kolom angka pada baris TOTAL
        $ws->getStyle("C{$last}:D{$last}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $ws->getRowDimension($last)->setRowHeight(24);

        $ws->getStyle("D5:D{$last}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

        $ws->getStyle("A4:D{$last}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF16A34A']]],
        ]);
    }
}

// ═══════════════════════════════════════════════════════════════
//  SHEET 4 ·  DETAIL PRODUK TERJUAL
// ═══════════════════════════════════════════════════════════════
class DetailProdukSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    protected array $produkRows    = [];
    protected array $subHeaderRows = [];
    protected array $detailRows    = [];
    protected array $subtotalRows  = [];
    protected array $spacerRows    = [];
    protected int   $grandTotalRow = 0;
    protected int   $mainHeaderRow = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function title(): string
    {
        return '📦 Detail Produk';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No (sempit - hanya angka)
            'B' => 18,  // Kode Produk
            'C' => 38,  // Nama Produk (lebar)
            'D' => 22,  // Kategori
            'E' => 13,  // Total Qty
            'F' => 11,  // Jml Trx
            'G' => 22,  // Harga Satuan
            'H' => 24,  // Total Penjualan
            'I' => 13,  // Avg Qty/Trx
        ];
    }

    public function array(): array
    {
        $rows = [];

        // Row 1: Banner
        $rows[] = ['WPOS · TOKO SAHABAT  —  DETAIL PRODUK TERJUAL', '', '', '', '', '', '', '', ''];
        // Row 2: Periode
        $rows[] = [
            'Periode:',
            Carbon::parse($this->data['tanggalMulai'])->format('d M Y')
                . '  –  '
                . Carbon::parse($this->data['tanggalSelesai'])->format('d M Y'),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
        // Row 3: spacer
        $this->spacerRows[] = 3;
        $rows[] = ['', '', '', '', '', '', '', '', ''];

        // Row 4: Statistik section header
        $rows[] = ['STATISTIK RINGKAS', '', '', '', '', '', '', '', ''];
        // Row 5: kolom header statistik
        $rows[] = ['Keterangan', '', '', 'Nilai', '', '', '', '', ''];
        // Row 6-9: data statistik (label di A, nilai di D)
        // FIX: label di sini konsisten dengan heading di styles() → tambahkan "(Rp)" pada baris omzet
        $rows[] = ['Jumlah Jenis Produk Terjual',            '', '', (int) $this->data['detailProduk']->count(), '', '', '', '', ''];
        $rows[] = ['Total Qty Terjual',                      '', '', (int) $this->data['detailProduk']->sum('total_qty'), '', '', '', '', ''];
        $rows[] = ['Total Omzet (Rp)',                       '', '', (float) $this->data['detailProduk']->sum('total_penjualan'), '', '', '', '', ''];
        $avg = $this->data['detailProduk']->count() > 0
            ? round($this->data['detailProduk']->sum('total_penjualan') / $this->data['detailProduk']->count()) : 0;
        $rows[] = ['Rata-rata Omzet per Produk (Rp)',        '', '', (float) $avg, '', '', '', '', ''];

        // Row 10: spacer
        $this->spacerRows[] = 10;
        $rows[] = ['', '', '', '', '', '', '', '', ''];

        // Row 11: header tabel utama
        $this->mainHeaderRow = 11;
        $rows[] = [
            'No',
            'Kode',
            'Nama Produk',
            'Kategori',
            'Total Qty',
            'Jml Trx',
            'Harga Satuan (Rp)',
            'Total Penjualan (Rp)',
            'Avg Qty/Trx',
        ];

        // ── Data produk (mulai row 12) ────────────────────────────
        foreach ($this->data['detailProduk'] as $i => $produk) {

            // Baris summary produk
            $this->produkRows[] = count($rows) + 1;
            $rows[] = [
                $i + 1,
                $produk->code_produk,
                $produk->nama_produk,
                $produk->nama_kategori ?? '-',
                (int)   $produk->total_qty,
                (int)   $produk->total_transaksi,
                (float) $produk->harga_satuan,
                (float) $produk->total_penjualan,
                round((float) $produk->rata_rata_qty, 2),
            ];

            if ($produk->detailTransaksi->isEmpty()) {
                $rows[] = ['', '', '— tidak ada detail transaksi —', '', '', '', '', '', ''];
                $this->spacerRows[] = count($rows) + 1;
                $rows[] = ['', '', '', '', '', '', '', '', ''];
                continue;
            }

            // Sub-header kolom detail
            $this->subHeaderRows[] = count($rows) + 1;
            $rows[] = [
                '',
                'No. Transaksi',
                'Tanggal & Waktu',
                'Kasir',
                'Qty',
                '',
                'Harga Jual (Rp)',
                'Subtotal (Rp)',
                '',
            ];

            // Baris transaksi detail
            $subtotalQty   = 0;
            $subtotalHarga = 0.0;
            foreach ($produk->detailTransaksi as $detail) {
                $this->detailRows[] = count($rows) + 1;
                $qty      = (int)   $detail->qty;
                $subtotal = (float) $detail->subtotal;
                $subtotalQty   += $qty;
                $subtotalHarga += $subtotal;
                $rows[] = [
                    '',
                    '#' . str_pad($detail->id_penjualan, 6, '0', STR_PAD_LEFT),
                    Carbon::parse($detail->tanggal_penjualan)->format('d/m/Y H:i'),
                    $detail->nama_kasir ?? 'Admin',
                    $qty,
                    '',
                    (float) $detail->harga_jual,
                    $subtotal,
                    '',
                ];
            }

            // Subtotal per produk
            $this->subtotalRows[] = count($rows) + 1;
            $rows[] = [
                '',
                '',
                'Subtotal  —  ' . $produk->nama_produk,
                '',
                $subtotalQty,
                '',
                '',
                $subtotalHarga,
                '',
            ];

            // Spacer tipis antar produk
            $this->spacerRows[] = count($rows) + 1;
            $rows[] = ['', '', '', '', '', '', '', '', ''];
        }

        // Grand Total
        $this->grandTotalRow = count($rows) + 1;
        $rows[] = [
            '',
            '',
            'GRAND TOTAL',
            '',
            (int)   $this->data['detailProduk']->sum('total_qty'),
            (int)   $this->data['detailProduk']->sum('total_transaksi'),
            '',
            (float) $this->data['detailProduk']->sum('total_penjualan'),
            '',
        ];

        return $rows;
    }

    public function styles(Worksheet $ws): void
    {
        // ── Banner ───────────────────────────────────────────────
        $ws->mergeCells('A1:I1');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(34);

        // ── Periode ──────────────────────────────────────────────
        $ws->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
        ]);
        $ws->getStyle('B2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
        ]);

        // ── Statistik section header (row 4) ─────────────────────
        $ws->mergeCells('A4:I4');
        $ws->getStyle('A4')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF9333EA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $ws->getRowDimension(4)->setRowHeight(22);

        // Stats header row 5
        $ws->mergeCells('A5:C5');
        $ws->mergeCells('D5:E5');
        $ws->getStyle('A5:E5')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF6B21A8']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEDE9FE']],
            'borders'   => [
                'bottom'     => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF9333EA']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $ws->getRowDimension(5)->setRowHeight(18);

        // Stats data rows 6-9
        // FIX: $statsLabels array dihapus karena tidak dipakai — label sudah ada di array()
        foreach ([6, 7, 8, 9] as $r) {
            $bg = $r % 2 === 0 ? 'FFF5F3FF' : 'FFFAF5FF';
            $ws->getStyle("A{$r}:I{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
            $ws->mergeCells("A{$r}:C{$r}");
            $ws->getStyle("A{$r}")->applyFromArray([
                'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            ]);
            $ws->mergeCells("D{$r}:E{$r}");
            $ws->getStyle("D{$r}")->applyFromArray([
                'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $ws->getRowDimension($r)->setRowHeight(20);
        }
        // Format angka Rp untuk baris omzet (row 8, 9)
        $ws->getStyle('D8')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->getStyle('D9')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->getStyle('A5:I9')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF9333EA']]],
        ]);

        // ── SPACER ROWS → putih bersih, tinggi mini ──────────────
        foreach ($this->spacerRows as $r) {
            $ws->getStyle("A{$r}:I{$r}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']],
            ]);
            $ws->getRowDimension($r)->setRowHeight(6);
        }

        // ── Header tabel utama (row 11) ───────────────────────────
        $mH = $this->mainHeaderRow;
        $ws->getStyle("A{$mH}:I{$mH}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF7E22CE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFEDE9FE']]],
        ]);
        $ws->getRowDimension($mH)->setRowHeight(24);
        $ws->freezePane('A12');

        // ── Summary produk (hijau tua) ────────────────────────────
        foreach ($this->produkRows as $r) {
            $ws->getStyle("A{$r}:I{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
                'borders' => [
                    'top'        => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF14532D']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['argb' => 'FF166534']],
                ],
            ]);
            // FIX: Alignment kanan untuk kolom angka di baris summary produk
            $ws->getStyle("E{$r}:I{$r}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getStyle("E{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("G{$r}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $ws->getStyle("H{$r}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $ws->getRowDimension($r)->setRowHeight(22);
        }

        // ── Sub-header kolom detail (hijau xlight) ────────────────
        foreach ($this->subHeaderRows as $r) {
            $ws->getStyle("A{$r}:I{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'bold' => true, 'italic' => true, 'size' => 9, 'color' => ['argb' => 'FF166534']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0FDF4']],
                'borders' => [
                    'bottom'     => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF86EFAC']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBBF7D0']],
                ],
            ]);
            $ws->getRowDimension($r)->setRowHeight(16);
        }

        // ── Baris transaksi detail (zebra hijau/putih) ────────────
        foreach ($this->detailRows as $idx => $r) {
            $bg = $idx % 2 === 0 ? 'FFF0FDF4' : 'FFFFFFFF';
            $ws->getStyle("A{$r}:I{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'size' => 9, 'color' => ['argb' => 'FF374151']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
            $ws->getStyle("B{$r}")->applyFromArray([
                'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 9, 'color' => ['argb' => 'FF2563EB']],
            ]);
            // FIX: Alignment kanan untuk kolom angka di baris detail
            $ws->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getStyle("G{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getStyle("E{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("G{$r}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $ws->getStyle("H{$r}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $ws->getRowDimension($r)->setRowHeight(17);
        }

        // ── Subtotal per produk (hijau sedang) ────────────────────
        foreach ($this->subtotalRows as $r) {
            $ws->mergeCells("C{$r}:D{$r}");
            $ws->getStyle("A{$r}:I{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF14532D']],
                    'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF14532D']],
                ],
            ]);
            // FIX: Alignment kanan untuk kolom angka subtotal
            $ws->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getStyle("H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getStyle("E{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("H{$r}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $ws->getRowDimension($r)->setRowHeight(20);
        }

        // ── Grand Total (hijau sangat tua + border double) ────────
        $gR = $this->grandTotalRow;
        $ws->mergeCells("C{$gR}:D{$gR}");
        $ws->getStyle("A{$gR}:I{$gR}")->applyFromArray([
            'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF14532D']],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF052E16']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF052E16']],
            ],
        ]);
        // FIX: Alignment kanan untuk kolom angka grand total
        $ws->getStyle("E{$gR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $ws->getStyle("F{$gR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $ws->getStyle("H{$gR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $ws->getStyle("E{$gR}")->getNumberFormat()->setFormatCode('#,##0');
        $ws->getStyle("H{$gR}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->getRowDimension($gR)->setRowHeight(28);
    }
}

// ═══════════════════════════════════════════════════════════════
//  SHEET 5 ·  PER KASIR
// ═══════════════════════════════════════════════════════════════
class PerKasirSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function title(): string
    {
        return '👤 Per Kasir';
    }

    public function array(): array
    {
        $rows = [
            ['WPOS · TOKO SAHABAT  —  LAPORAN PER KASIR'],
            [
                'Periode:',
                Carbon::parse($this->data['tanggalMulai'])->format('d M Y')
                    . '  –  '
                    . Carbon::parse($this->data['tanggalSelesai'])->format('d M Y'),
            ],
            [],
            ['No.', 'Nama Kasir', 'Total Transaksi', 'Total Penjualan (Rp)'],
        ];

        $totalTrxAll         = 0;
        $totalPenjualanKasir = 0.0;
        foreach ($this->data['laporanPerKasir'] as $i => $kasir) {
            $totalTrxAll         += (int)   $kasir->total_transaksi;
            $totalPenjualanKasir += (float) $kasir->total_penjualan;
            $rows[] = [
                $i + 1,
                $kasir->nama_kasir,
                (int)   $kasir->total_transaksi,
                (float) $kasir->total_penjualan,
            ];
        }

        $rows[] = ['', 'TOTAL', $totalTrxAll, $totalPenjualanKasir];

        return $rows;
    }

    public function styles(Worksheet $ws): void
    {
        $last = $ws->getHighestRow();

        // Banner
        $ws->mergeCells('A1:D1');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(34);

        $ws->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
        ]);

        // Header kolom (row 4)
        $ws->getStyle('A4:D4')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(4)->setRowHeight(22);

        // FIX: Freeze pane agar header tetap terlihat saat scroll
        $ws->freezePane('A5');

        // Data rows
        for ($r = 5; $r < $last; $r++) {
            $bg = $r % 2 === 0 ? 'FFDCFCE7' : 'FFF0FDF4';
            $ws->getStyle("A{$r}:D{$r}")->applyFromArray([
                'font'    => ['name' => 'Calibri', 'size' => 10, 'color' => ['argb' => 'FF1E293B']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
            // Nama kasir bold hijau
            $ws->getStyle("B{$r}")->applyFromArray([
                'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
            ]);
            // FIX: Alignment kanan untuk kolom angka (Total Transaksi & Total Penjualan)
            $ws->getStyle("C{$r}:D{$r}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ws->getRowDimension($r)->setRowHeight(18);
        }

        // Total row
        $ws->getStyle("A{$last}:D{$last}")->applyFromArray([
            'font'    => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF14532D']]],
        ]);
        // FIX: Alignment kanan kolom angka pada baris TOTAL
        $ws->getStyle("C{$last}:D{$last}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $ws->getRowDimension($last)->setRowHeight(24);

        $ws->getStyle("D5:D{$last}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

        $ws->getStyle("A4:D{$last}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF16A34A']]],
        ]);
    }
}

// ═══════════════════════════════════════════════════════════════
//  MAIN EXPORT CLASS
// ═══════════════════════════════════════════════════════════════
class LaporanExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new RingkasanSheet($this->data),
            new TransaksiSheet($this->data),
            new ProdukTerlarisSheet($this->data),
            new DetailProdukSheet($this->data),
            new PerKasirSheet($this->data),
        ];
    }
}
