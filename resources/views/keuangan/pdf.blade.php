<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Point of Sale</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #4f46e5;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .header p {
            color: #888;
            font-size: 11px;
        }

        .period {
            background: #f3f4f6;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .stats {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-value.positive {
            color: #16a34a;
        }

        .stat-value.negative {
            color: #dc2626;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 25px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #4f46e5;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        tr:hover {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .total-row {
            background: #f3f4f6;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #888;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #6b7280;
            font-style: italic;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>POINT OF SALE</h1>
        <h2>Laporan Keuangan</h2>
        <p>Jl. Contoh No. 123, Surabaya | Telp: (031) 1234567</p>
    </div>

    <!-- Period -->
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-item">
            <div class="stat-label">Total Pemasukan</div>
            <div class="stat-value positive">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Pengeluaran</div>
            <div class="stat-value negative">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Saldo Bersih</div>
            <div class="stat-value {{ $saldoBersih >= 0 ? 'positive' : 'negative' }}">
                Rp {{ number_format($saldoBersih, 0, ',', '.') }}
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ number_format($totalTransaksi) }}</div>
        </div>
    </div>

    <!-- Detail Pemasukan -->
    @if ($pemasukan->count() > 0)
        <div class="section-title">💰 Detail Pemasukan</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 55%;">Jenis Pemasukan</th>
                    <th style="width: 20%;" class="text-center">Jumlah</th>
                    <th style="width: 20%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pemasukan as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->jenis_keuangan }}</td>
                        <td class="text-center">
                            <span class="badge badge-blue">{{ number_format($item->jumlah) }}x</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL PEMASUKAN</td>
                    <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="section-title">💰 Detail Pemasukan</div>
        <div class="no-data">Tidak ada data pemasukan pada periode ini</div>
    @endif

    <!-- Detail Pengeluaran -->
    @if ($pengeluaran->count() > 0)
        <div class="section-title">💸 Detail Pengeluaran</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 55%;">Jenis Pengeluaran</th>
                    <th style="width: 20%;" class="text-center">Jumlah</th>
                    <th style="width: 20%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengeluaran as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->jenis_keuangan }}</td>
                        <td class="text-center">
                            <span class="badge badge-blue">{{ number_format($item->jumlah) }}x</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL PENGELUARAN</td>
                    <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="section-title">💸 Detail Pengeluaran</div>
        <div class="no-data">Tidak ada data pengeluaran pada periode ini</div>
    @endif

    <!-- Riwayat Transaksi -->
    <div class="section-title">📝 Riwayat Transaksi Keuangan</div>
    @if ($transaksi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">Tanggal</th>
                    <th style="width: 42%;">Jenis</th>
                    <th style="width: 20%;" class="text-right">Nominal</th>
                    <th style="width: 15%;" class="text-center">Tipe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi as $index => $item)
                    @php
                        // Ambil tanggal dari relasi
                        $tanggal = null;
                        $jenisKeuangan = $item->jenis->jenis_keuangan ?? '-';

                        if ($item->penjualan) {
                            $tanggal = $item->penjualan->tanggal_penjualan;
                        } elseif ($item->penerimaan) {
                            $tanggal = $item->penerimaan->tanggal_penerimaan;
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $jenisKeuangan }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_keuangan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if (str_contains($jenisKeuangan, 'PEMASUKAN'))
                                <span class="badge badge-success">Pemasukan</span>
                            @else
                                <span class="badge badge-danger">Pengeluaran</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada transaksi pada periode ini</div>
    @endif

    <!-- Summary Final -->
    <table style="margin-top: 30px;">
        <tbody>
            <tr>
                <td width="70%" class="text-right"><strong>Total Pemasukan</strong></td>
                <td width="30%" class="text-right" style="color: #16a34a;"><strong>Rp
                        {{ number_format($totalPemasukan, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="text-right"><strong>Total Pengeluaran</strong></td>
                <td class="text-right" style="color: #dc2626;"><strong>Rp
                        {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
            </tr>
            <tr style="background: #f3f4f6; font-size: 14px;">
                <td class="text-right" style="padding: 12px 8px;"><strong>SALDO BERSIH</strong></td>
                <td class="text-right"
                    style="padding: 12px 8px; color: {{ $saldoBersih >= 0 ? '#16a34a' : '#dc2626' }};"><strong>Rp
                        {{ number_format($saldoBersih, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>© 2026 Point of Sale - Sistem Point of Sale</p>
    </div>

    <!-- Auto print script -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
