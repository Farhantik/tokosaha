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
     * Export laporan keuangan to Excel (XLSX format)
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

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Keuangan")
            ->setTitle("Laporan Keuangan")
            ->setSubject("Laporan Keuangan")
            ->setDescription("Laporan Keuangan periode " . $tanggalMulai . " s/d " . $tanggalSelesai);

        // Header - Judul Laporan
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Periode
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($tanggalMulai)) . ' s/d ' . date('d/m/Y', strtotime($tanggalSelesai)));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ringkasan
        $row = 4;
        $sheet->setCellValue('A' . $row, 'RINGKASAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Pemasukan');
        $sheet->setCellValue('B' . $row, 'Rp ' . number_format($totalPemasukan, 0, ',', '.'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Pengeluaran');
        $sheet->setCellValue('B' . $row, 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Saldo Bersih');
        $sheet->setCellValue('B' . $row, 'Rp ' . number_format($saldoBersih, 0, ',', '.'));
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Transaksi');
        $sheet->setCellValue('B' . $row, $totalTransaksi);
        $row += 2;

        // Tabel Transaksi
        $sheet->setCellValue('A' . $row, 'DETAIL TRANSAKSI');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        // Header tabel
        $headers = ['No', 'Tanggal', 'Jenis', 'Keterangan', 'Pemasukan', 'Pengeluaran'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD3D3D3');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $row++;

        // Data transaksi
        $no = 1;
        foreach ($transaksi as $item) {
            $tanggal = $item->penjualan ? $item->penjualan->tanggal_penjualan : 
                       ($item->penerimaan ? $item->penerimaan->tanggal_penerimaan : '-');
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($tanggal)));
            $sheet->setCellValue('C' . $row, $item->jenis->jenis_keuangan ?? '-');
            $sheet->setCellValue('D' . $row, $item->keterangan ?? '-');
            
            if (stripos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false) {
                $sheet->setCellValue('E' . $row, $item->total_keuangan);
                $sheet->setCellValue('F' . $row, 0);
            } else {
                $sheet->setCellValue('E' . $row, 0);
                $sheet->setCellValue('F' . $row, $item->total_keuangan);
            }
            
            // Format currency
            $sheet->getStyle('E' . $row)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle('F' . $row)->getNumberFormat()
                ->setFormatCode('#,##0');
            
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'laporan_keuangan_' . date('Y-m-d_His') . '.xlsx';

        // Save to temporary file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        // Return download response
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    private function getKeuanganData($tanggalMulai, $tanggalSelesai)
    {
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

        return [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoBersih' => $totalPemasukan - $totalPengeluaran,
            'transaksi' => $transaksi
        ];
    }
}
