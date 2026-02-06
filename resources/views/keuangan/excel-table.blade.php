<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .summary-table {
            width: 50%;
            margin-bottom: 20px;
        }
        .summary-table th {
            background-color: #2196F3;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>LAPORAN KEUANGAN</h2>
        <p>Periode: {{ date('d/m/Y', strtotime($tanggalMulai)) }} - {{ date('d/m/Y', strtotime($tanggalSelesai)) }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Ringkasan Keuangan -->
    <h3>Ringkasan Keuangan</h3>
    <table class="summary-table">
        <tr>
            <th>Keterangan</th>
            <th class="text-right">Jumlah</th>
        </tr>
        <tr>
            <td>Total Pemasukan</td>
            <td class="text-right positive">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Pengeluaran</td>
            <td class="text-right negative">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Saldo Bersih</td>
            <td class="text-right {{ $saldoBersih >= 0 ? 'positive' : 'negative' }}">
                Rp {{ number_format($saldoBersih, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Total Transaksi</td>
            <td class="text-right">{{ number_format($totalTransaksi, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Detail Pemasukan per Jenis -->
    @if($pemasukan->count() > 0)
    <h3>Detail Pemasukan per Jenis</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pemasukan</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemasukan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->jenis_keuangan }}</td>
                <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL PEMASUKAN</td>
                <td class="text-center">{{ number_format($pemasukan->sum('jumlah'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pemasukan->sum('total'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Detail Pengeluaran per Jenis -->
    @if($pengeluaran->count() > 0)
    <h3>Detail Pengeluaran per Jenis</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pengeluaran</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengeluaran as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->jenis_keuangan }}</td>
                <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL PENGELUARAN</td>
                <td class="text-center">{{ number_format($pengeluaran->sum('jumlah'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pengeluaran->sum('total'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Riwayat Transaksi -->
    <h3>Riwayat Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    @if($item->penjualan)
                        {{ date('d/m/Y H:i', strtotime($item->penjualan->tanggal_penjualan)) }}
                    @elseif($item->penerimaan)
                        {{ date('d/m/Y H:i', strtotime($item->penerimaan->tanggal_penerimaan)) }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->jenis->jenis_keuangan ?? '-' }}</td>
                <td>
                    @if($item->penjualan)
                        Penjualan #{{ $item->penjualan->id_penjualan }}
                    @elseif($item->penerimaan)
                        Penerimaan #{{ $item->penerimaan->id_penerimaan }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right {{ strpos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false ? 'positive' : 'negative' }}">
                    Rp {{ number_format($item->total_keuangan, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top: 40px;">
        <p><i>Laporan ini digenerate secara otomatis oleh sistem</i></p>
    </div>
</body>
</html>