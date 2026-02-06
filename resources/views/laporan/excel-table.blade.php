<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns="http://www.w3.org/TR/REC-html40">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body {
            font-family: Calibri, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #333333;
            padding: 10px 12px;
            font-size: 11pt;
        }

        th {
            background-color: #4472C4;
            color: #FFFFFF;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        /* Header Styles */
        .title-main {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #FFFFFF;
            border: none;
        }

        .subtitle {
            text-align: center;
            padding: 8px;
            background-color: #F3F4F6;
            font-size: 10pt;
            color: #374151;
            border: 1px solid #D1D5DB;
        }

        /* Section Headers */
        .section-header {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: #FFFFFF;
            font-weight: bold;
            font-size: 13pt;
            padding: 12px;
            text-align: center;
            border: none;
        }

        /* Stats Section */
        .stats-label {
            background-color: #FEF3C7;
            font-weight: bold;
            padding: 10px;
            color: #92400E;
        }

        .stats-value {
            background-color: #FFFBEB;
            text-align: right;
            font-weight: bold;
            color: #1F2937;
            padding: 10px;
        }

        /* Table Headers */
        .table-header {
            background-color: #3B82F6;
            color: #FFFFFF;
            font-weight: bold;
            text-align: center;
            padding: 10px;
        }

        /* Total Row */
        .total-row {
            background-color: #E5E7EB;
            font-weight: bold;
            color: #1F2937;
        }

        .total-label {
            text-align: right;
            padding-right: 15px;
        }

        /* Text Alignment */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Numbering */
        .number-col {
            text-align: center;
            background-color: #F9FAFB;
            font-weight: 500;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            font-style: italic;
            color: #6B7280;
            padding: 20px;
            background-color: #F9FAFB;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-style: italic;
            color: #6B7280;
            padding: 15px;
            background-color: #F3F4F6;
            border: 1px solid #D1D5DB;
            font-size: 9pt;
        }

        /* Zebra Striping */
        .odd-row {
            background-color: #FFFFFF;
        }

        .even-row {
            background-color: #F9FAFB;
        }

        /* Highlight */
        .highlight {
            background-color: #DBEAFE;
        }

        /* Currency */
        .currency {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>

<body>

    <!-- ========== HEADER ========== -->
    <table>
        <tr>
            <td colspan="6" class="title-main">
                📊 LAPORAN PENJUALAN - TOKO SAHABAT
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="6" class="subtitle">
                <strong>Periode:</strong>
                {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} -
                {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="subtitle">
                <strong>Dicetak:</strong> {{ now()->format('d F Y, H:i:s') }} WIB
            </td>
        </tr>
    </table>

    <!-- ========== RINGKASAN STATISTIK ========== -->
    <table>
        <tr>
            <td colspan="6" class="section-header">
                📈 RINGKASAN STATISTIK
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th class="table-header" width="40%">Keterangan</th>
            <th class="table-header" width="60%">Nilai</th>
        </tr>
        <tr>
            <td class="stats-label">💰 Total Penjualan</td>
            <td class="stats-value currency">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="stats-label">🧾 Total Transaksi</td>
            <td class="stats-value">{{ number_format($totalTransaksi, 0, ',', '.') }} transaksi</td>
        </tr>
        <tr>
            <td class="stats-label">💵 Total Kembalian</td>
            <td class="stats-value currency">Rp {{ number_format($totalKembalian, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="stats-label">📊 Rata-rata per Transaksi</td>
            <td class="stats-value currency">
                Rp {{ number_format($totalTransaksi > 0 ? $totalPenjualan / $totalTransaksi : 0, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <!-- ========== PRODUK TERLARIS ========== -->
    <table>
        <tr>
            <td colspan="6" class="section-header">
                🏆 PRODUK TERLARIS
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th class="table-header" width="10%">No</th>
            <th class="table-header" width="50%">Nama Produk</th>
            <th class="table-header" width="20%">Qty Terjual</th>
            <th class="table-header" width="20%">Total Penjualan</th>
        </tr>
        @forelse($produkTerlaris as $index => $produk)
            <tr class="{{ $index % 2 == 0 ? 'even-row' : 'odd-row' }}">
                <td class="number-col">{{ $index + 1 }}</td>
                <td class="text-left">
                    @if ($index < 3)
                        <strong>{{ $produk->nama_produk }}</strong>
                    @else
                        {{ $produk->nama_produk }}
                    @endif
                </td>
                <td class="text-center">{{ number_format($produk->total_qty, 0, ',', '.') }}</td>
                <td class="text-right currency">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty-state">
                    Tidak ada data produk pada periode ini
                </td>
            </tr>
        @endforelse
    </table>

    <!-- ========== LAPORAN PER KASIR ========== -->
    <table>
        <tr>
            <td colspan="6" class="section-header">
                👤 LAPORAN PER KASIR
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th class="table-header" width="10%">No</th>
            <th class="table-header" width="40%">Nama Kasir</th>
            <th class="table-header" width="25%">Total Transaksi</th>
            <th class="table-header" width="25%">Total Penjualan</th>
        </tr>
        @forelse($laporanPerKasir as $index => $kasir)
            <tr class="{{ $index % 2 == 0 ? 'even-row' : 'odd-row' }}">
                <td class="number-col">{{ $index + 1 }}</td>
                <td class="text-left"><strong>{{ $kasir->nama_kasir }}</strong></td>
                <td class="text-center">{{ number_format($kasir->total_transaksi, 0, ',', '.') }}</td>
                <td class="text-right currency">Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty-state">
                    Tidak ada data kasir pada periode ini
                </td>
            </tr>
        @endforelse
    </table>

    <!-- ========== RIWAYAT TRANSAKSI ========== -->
    <table>
        <tr>
            <td colspan="6" class="section-header">
                📋 RIWAYAT TRANSAKSI DETAIL
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th class="table-header" width="15%">No. Transaksi</th>
            <th class="table-header" width="15%">Tanggal</th>
            <th class="table-header" width="18%">Kasir</th>
            <th class="table-header" width="17%">Total Pembayaran</th>
            <th class="table-header" width="17%">Total Bayar</th>
            <th class="table-header" width="18%">Kembalian</th>
        </tr>
        @forelse($transaksi as $index => $item)
            <tr class="{{ $index % 2 == 0 ? 'even-row' : 'odd-row' }}">
                <td class="text-center"><strong>#{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}</strong></td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i') }}</td>
                <td class="text-left">{{ $item->kasir ?? 'Admin' }}</td>
                <td class="text-right currency">Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}</td>
                <td class="text-right currency">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td class="text-right currency">Rp {{ number_format($item->kembalian_pembayaran, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty-state">
                    Tidak ada transaksi pada periode ini
                </td>
            </tr>
        @endforelse

        @if ($transaksi->count() > 0)
            <tr class="total-row">
                <td colspan="3" class="total-label"><strong>GRAND TOTAL</strong></td>
                <td class="text-right currency"><strong>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right currency"><strong>Rp
                        {{ number_format($transaksi->sum('total_bayar'), 0, ',', '.') }}</strong></td>
                <td class="text-right currency"><strong>Rp {{ number_format($totalKembalian, 0, ',', '.') }}</strong>
                </td>
            </tr>
        @endif
    </table>

    <!-- ========== FOOTER ========== -->
    <table>
        <tr>
            <td colspan="6" class="footer">
                © {{ now()->year }} Toko Sahabat - Sistem Point of Sale | Laporan ini dibuat secara otomatis
            </td>
        </tr>
    </table>

</body>

</html>
