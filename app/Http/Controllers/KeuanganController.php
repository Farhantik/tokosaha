<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan;
use App\Models\KeuanganJenis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal (30 hari terakhir)
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(30)->format('Y-m-d'));
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        // Total Pemasukan (dari penjualan)
        $totalPemasukan = Keuangan::query()
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PEMASUKAN%')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('keuangan.total_keuangan');

        // Total Pengeluaran (dari penerimaan)
        $totalPengeluaran = Keuangan::query()
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PENGELUARAN%')
            ->whereBetween('penerimaan.tanggal_penerimaan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('keuangan.total_keuangan');

        // Saldo Bersih
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        // Total Transaksi
        $totalTransaksi = Keuangan::query()
            ->leftJoin('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('penjualan.tanggal_penjualan', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalSelesai . ' 23:59:59'
                ])
                    ->orWhereBetween('penerimaan.tanggal_penerimaan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
            })
            ->count();

        // Detail Pemasukan per Jenis
        $pemasukan = Keuangan::query()
            ->select(
                'keuangan_jenis.jenis_keuangan',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(keuangan.total_keuangan) as total')
            )
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PEMASUKAN%')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('keuangan_jenis.jenis_keuangan')
            ->get();

        // Detail Pengeluaran per Jenis
        $pengeluaran = Keuangan::query()
            ->select(
                'keuangan_jenis.jenis_keuangan',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(keuangan.total_keuangan) as total')
            )
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PENGELUARAN%')
            ->whereBetween('penerimaan.tanggal_penerimaan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('keuangan_jenis.jenis_keuangan')
            ->get();

        // Keuangan per Hari (untuk grafik)
        $keuanganPerHari = DB::table('keuangan')
            ->leftJoin('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->leftJoin('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->select(
                DB::raw('COALESCE(DATE(penjualan.tanggal_penjualan), DATE(penerimaan.tanggal_penerimaan)) as tanggal'),
                DB::raw('SUM(CASE WHEN keuangan_jenis.jenis_keuangan LIKE "PEMASUKAN%" THEN keuangan.total_keuangan ELSE 0 END) as pemasukan'),
                DB::raw('SUM(CASE WHEN keuangan_jenis.jenis_keuangan LIKE "PENGELUARAN%" THEN keuangan.total_keuangan ELSE 0 END) as pengeluaran')
            )
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('penjualan.tanggal_penjualan', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalSelesai . ' 23:59:59'
                ])
                    ->orWhereBetween('penerimaan.tanggal_penerimaan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Riwayat Transaksi
        $transaksi = Keuangan::with(['jenis', 'penjualan', 'penerimaan', 'kasir'])
            ->leftJoin('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->select('keuangan.*')
            ->selectRaw('COALESCE(penjualan.tanggal_penjualan, penerimaan.tanggal_penerimaan) as created_at')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('penjualan.tanggal_penjualan', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalSelesai . ' 23:59:59'
                ])
                    ->orWhereBetween('penerimaan.tanggal_penerimaan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('keuangan.index', compact(
            'tanggalMulai',
            'tanggalSelesai',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoBersih',
            'totalTransaksi',
            'pemasukan',
            'pengeluaran',
            'keuanganPerHari',
            'transaksi'
        ));
    }

    /**
     * Export laporan keuangan to PDF
     */
    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(30)->format('Y-m-d'));
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        // Get all data without pagination
        $totalPemasukan = Keuangan::query()
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PEMASUKAN%')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('keuangan.total_keuangan');

        $totalPengeluaran = Keuangan::query()
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PENGELUARAN%')
            ->whereBetween('penerimaan.tanggal_penerimaan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('keuangan.total_keuangan');

        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        // Get all transactions without pagination
        $transaksi = Keuangan::with(['jenis', 'penjualan', 'penerimaan'])
            ->leftJoin('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->select('keuangan.*')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('penjualan.tanggal_penjualan', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalSelesai . ' 23:59:59'
                ])
                    ->orWhereBetween('penerimaan.tanggal_penerimaan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
            })
            ->orderByRaw('COALESCE(penjualan.tanggal_penjualan, penerimaan.tanggal_penerimaan) DESC')
            ->get();

        // Total Transaksi (hitung dari collection)
        $totalTransaksi = $transaksi->count();

        // Detail Pemasukan per Jenis
        $pemasukan = Keuangan::query()
            ->select(
                'keuangan_jenis.jenis_keuangan',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(keuangan.total_keuangan) as total')
            )
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PEMASUKAN%')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('keuangan_jenis.jenis_keuangan')
            ->get();

        // Detail Pengeluaran per Jenis
        $pengeluaran = Keuangan::query()
            ->select(
                'keuangan_jenis.jenis_keuangan',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(keuangan.total_keuangan) as total')
            )
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PENGELUARAN%')
            ->whereBetween('penerimaan.tanggal_penerimaan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->groupBy('keuangan_jenis.jenis_keuangan')
            ->get();

        // Generate PDF dengan DomPDF
        $pdf = Pdf::loadView('keuangan.pdf', compact(
            'transaksi',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoBersih',
            'totalTransaksi',
            'pemasukan',
            'pengeluaran',
            'tanggalMulai',
            'tanggalSelesai'
        ));

        // Set paper size dan orientation
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = 'laporan_keuangan_' . date('Y-m-d_His') . '.pdf';

        // Download PDF
        return $pdf->download($filename);
    }

    /**
     * Export laporan keuangan to Excel (XLSX format) - REDESIGNED WITH GREEN THEME
     */
    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(30)->format('Y-m-d'));
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        // Total Pemasukan
        $totalPemasukan = Keuangan::query()
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PEMASUKAN%')
            ->whereBetween('penjualan.tanggal_penjualan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('keuangan.total_keuangan');

        // Total Pengeluaran
        $totalPengeluaran = Keuangan::query()
            ->join('keuangan_jenis', 'keuangan.id_keuangan_jenis', '=', 'keuangan_jenis.id_keuangan_jenis')
            ->join('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->where('keuangan_jenis.jenis_keuangan', 'LIKE', 'PENGELUARAN%')
            ->whereBetween('penerimaan.tanggal_penerimaan', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59'
            ])
            ->sum('keuangan.total_keuangan');

        // Saldo Bersih
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        // Get all transactions without pagination
        $transaksi = Keuangan::with(['jenis', 'penjualan', 'penerimaan'])
            ->leftJoin('penjualan', 'keuangan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('penerimaan', 'keuangan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->select('keuangan.*')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('penjualan.tanggal_penjualan', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalSelesai . ' 23:59:59'
                ])
                    ->orWhereBetween('penerimaan.tanggal_penerimaan', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
            })
            ->orderByRaw('COALESCE(penjualan.tanggal_penjualan, penerimaan.tanggal_penerimaan) DESC')
            ->get();

        // Total Transaksi
        $totalTransaksi = $transaksi->count();

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Keuangan")
            ->setTitle("Laporan Keuangan")
            ->setSubject("Laporan Keuangan")
            ->setDescription("Laporan Keuangan periode " . $tanggalMulai . " s/d " . $tanggalSelesai);

        $row = 1;

        // ═══════════════════════════════════════════════════════
        // HEADER UTAMA
        // ═══════════════════════════════════════════════════════
        $sheet->setCellValue('A' . $row, 'LAPORAN KEUANGAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '15803d']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        // Periode
        $sheet->setCellValue('A' . $row, 'Periode: ' . date('d/m/Y', strtotime($tanggalMulai)) . ' s/d ' . date('d/m/Y', strtotime($tanggalSelesai)));
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => [
                'size' => 11,
                'color' => ['rgb' => 'd1fae5']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '166534']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;

        // Tanggal Cetak
        $sheet->setCellValue('A' . $row, 'Dicetak pada: ' . date('d F Y H:i:s') . ' WIB');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => [
                'size' => 9,
                'color' => ['rgb' => '86efac']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '14532d']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $row += 2;

        // ═══════════════════════════════════════════════════════
        // RINGKASAN KEUANGAN
        // ═══════════════════════════════════════════════════════
        $sheet->setCellValue('A' . $row, 'RINGKASAN KEUANGAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '15803d']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;

        // Total Pemasukan
        $sheet->setCellValue('A' . $row, 'Total Pemasukan');
        $sheet->setCellValue('D' . $row, 'Rp ' . number_format($totalPemasukan, 0, ',', '.'));
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f0fdf4']
            ]
        ]);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle('D' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => '15803d']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $row++;

        // Total Pengeluaran
        $sheet->setCellValue('A' . $row, 'Total Pengeluaran');
        $sheet->setCellValue('D' . $row, 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'fff1f2']
            ]
        ]);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle('D' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => 'dc2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $row++;

        // Saldo Bersih
        $sheet->setCellValue('A' . $row, 'Saldo Bersih');
        $sheet->setCellValue('D' . $row, 'Rp ' . number_format($saldoBersih, 0, ',', '.'));
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $saldoColor = $saldoBersih >= 0 ? 'f0fdfa' : 'fef2f2';
        $saldoTextColor = $saldoBersih >= 0 ? '0f766e' : 'dc2626';
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $saldoColor]
            ]
        ]);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle('D' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => $saldoTextColor]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $row++;

        // Total Transaksi
        $sheet->setCellValue('A' . $row, 'Total Transaksi');
        $sheet->setCellValue('D' . $row, number_format($totalTransaksi, 0, ',', '.') . ' transaksi');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'eff6ff']
            ]
        ]);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle('D' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => '1d4ed8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $row += 2;

        // ═══════════════════════════════════════════════════════
        // DETAIL TRANSAKSI
        // ═══════════════════════════════════════════════════════
        $sheet->setCellValue('A' . $row, 'DETAIL TRANSAKSI');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '15803d']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;

        // Header tabel
        $headers = ['No', 'Tanggal', 'Jenis', 'Keterangan', 'Pemasukan', 'Pengeluaran'];
        $headerCols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($headerCols as $index => $col) {
            $sheet->setCellValue($col . $row, $headers[$index]);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['rgb' => '14532d']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'dcfce7']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
        }
        $row++;

        // Data transaksi
        $no = 1;
        foreach ($transaksi as $item) {
            $isPemasukan = stripos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false;
            $bgColor = ($no % 2 == 0) ? 'f9fffe' : 'ffffff';

            $tanggal = $item->penjualan ? $item->penjualan->tanggal_penjualan : ($item->penerimaan ? $item->penerimaan->tanggal_penerimaan : '-');

            $keterangan = '-';
            if ($item->penjualan) {
                $keterangan = 'Penjualan #' . $item->penjualan->id_penjualan;
            } elseif ($item->penerimaan) {
                $keterangan = 'Penerimaan #' . $item->penerimaan->id_penerimaan;
            }

            // No
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
            ]);

            // Tanggal
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($tanggal)));
            $sheet->getStyle('B' . $row)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
            ]);

            // Jenis
            $sheet->setCellValue('C' . $row, $item->jenis->jenis_keuangan ?? '-');
            $sheet->getStyle('C' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
            ]);

            // Keterangan
            $sheet->setCellValue('D' . $row, $keterangan);
            $sheet->getStyle('D' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
            ]);

            // Pemasukan / Pengeluaran
            if ($isPemasukan) {
                $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item->total_keuangan, 0, ',', '.'));
                $sheet->setCellValue('F' . $row, '-');
                $sheet->getStyle('E' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '15803d']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
                ]);
            } else {
                $sheet->setCellValue('E' . $row, '-');
                $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item->total_keuangan, 0, ',', '.'));
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'dc2626']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
                ]);
            }

            $sheet->getStyle('F' . $row)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
            ]);

            $row++;
        }

        // Total Row
        $totalPemasukanSum = $transaksi->filter(function ($t) {
            return stripos($t->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false;
        })->sum('total_keuangan');

        $totalPengeluaranSum = $transaksi->filter(function ($t) {
            return stripos($t->jenis->jenis_keuangan ?? '', 'PEMASUKAN') === false;
        })->sum('total_keuangan');

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, 'Rp ' . number_format($totalPemasukanSum, 0, ',', '.'));
        $sheet->setCellValue('F' . $row, 'Rp ' . number_format($totalPengeluaranSum, 0, ',', '.'));

        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'dcfce7']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '15803d']]
            ]
        ]);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $sheet->getStyle('E' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => '15803d']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $sheet->getStyle('F' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => 'dc2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);
        $row += 2;

        // Footer
        $sheet->setCellValue('A' . $row, 'Laporan ini digenerate secara otomatis oleh sistem pada ' . date('d F Y H:i:s') . ' WIB - Dokumen ini sah tanpa tanda tangan dan meterai');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '6b7280']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '15803d']]
            ]
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Generate filename
        $filename = 'laporan_keuangan_' . date('Y-m-d_His') . '.xlsx';

        // Save to temporary file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        // Return download response
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
