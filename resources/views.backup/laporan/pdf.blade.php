<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Toko Sahabat</title>
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
            width: 33.33%;
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

        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
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
        <h1>TOKO SAHABAT</h1>
        <h2>Laporan Penjualan</h2>
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
            <div class="stat-label">Total Penjualan</div>
            <div class="stat-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $totalTransaksi }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Kembalian</div>
            <div class="stat-value">Rp {{ number_format($totalKembalian, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="section-title">📊 Produk Terlaris</div>
    @if ($produkTerlaris->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 55%;">Nama Produk</th>
                    <th style="width: 20%;" class="text-center">Qty Terjual</th>
                    <th style="width: 20%;" class="text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produkTerlaris as $index => $produk)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $produk->nama_produk }}</td>
                        <td class="text-center">
                            <span class="badge badge-blue">{{ $produk->total_qty }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada data produk terlaris</div>
    @endif

    <!-- Laporan Per Kasir -->
    <div class="section-title">👥 Laporan Per Kasir</div>
    @if ($laporanPerKasir->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Nama Kasir</th>
                    <th style="width: 25%;" class="text-center">Total Transaksi</th>
                    <th style="width: 20%;" class="text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporanPerKasir as $index => $kasir)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $kasir->nama_kasir }}</td>
                        <td class="text-center">
                            <span class="badge badge-green">{{ $kasir->total_transaksi }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada data kasir</div>
    @endif

    <!-- Riwayat Transaksi -->
    <div class="section-title">📝 Riwayat Transaksi</div>
    @if ($transaksi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">No. Transaksi</th>
                    <th style="width: 18%;">Tanggal</th>
                    <th style="width: 20%;">Kasir</th>
                    <th style="width: 16%;" class="text-right">Total</th>
                    <th style="width: 16%;" class="text-right">Bayar</th>
                    <th style="width: 18%;" class="text-right">Kembalian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi as $item)
                    <tr>
                        <td>#{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->kasir ?? 'Admin' }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->kembalian_pembayaran, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada transaksi pada periode ini</div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>© 2025 Toko Sahabat - Sistem Point of Sale</p>
    </div>

    <!-- Auto print script -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
