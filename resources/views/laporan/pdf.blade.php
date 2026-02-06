<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2196F3;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2196F3;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .header .periode {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .header .print-date {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }
        
        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 15px;
            margin: 25px 0 15px 0;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .summary-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .summary-table tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .summary-table tr:last-child {
            border-bottom: none;
        }
        
        .summary-table td {
            padding: 12px 15px;
        }
        
        .summary-table .label {
            font-weight: bold;
            color: #555;
            width: 40%;
        }
        
        .summary-table .value {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
        
        .summary-table .green { color: #4CAF50; }
        .summary-table .red { color: #f44336; }
        .summary-table .blue { color: #2196F3; }
        .summary-table .orange { color: #FF9800; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
        }
        
        table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        table thead th {
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }
        
        table tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        table tbody tr:hover {
            background: #f0f0f0;
        }
        
        table tbody td {
            padding: 10px;
            font-size: 12px;
        }
        
        table tfoot tr {
            background: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #2196F3;
        }
        
        table tfoot td {
            padding: 12px 10px;
            font-size: 13px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-success { background: #4CAF50; color: white; }
        .badge-danger { background: #f44336; color: white; }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
            background: #f9f9f9;
            border-radius: 5px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2196F3;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        
        @media print {
            body {
                padding: 0;
                background: white;
            }
            
            .container {
                box-shadow: none;
                padding: 0;
            }
            
            table tbody tr:hover {
                background: inherit !important;
            }
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 LAPORAN PENJUALAN</h1>
            <p class="periode">
                Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
            </p>
            <p class="print-date">Dicetak pada: {{ date('d F Y H:i:s') }} WIB</p>
        </div>

        <!-- Ringkasan Penjualan -->
        <div class="section-title">💰 RINGKASAN PENJUALAN</div>
        <table class="summary-table">
            <tr>
                <td class="label">Total Penjualan:</td>
                <td class="value green">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Transaksi:</td>
                <td class="value orange">{{ number_format($totalTransaksi, 0, ',', '.') }} transaksi</td>
            </tr>
            <tr>
                <td class="label">Total Kembalian:</td>
                <td class="value red">Rp {{ number_format($totalKembalian, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Penjualan Bersih:</td>
                <td class="value blue">Rp {{ number_format($totalPenjualan - $totalKembalian, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Produk Terlaris -->
        <div class="section-title">🏆 TOP 10 PRODUK TERLARIS</div>
        @if($produkTerlaris->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Produk</th>
                    <th class="text-center" style="width: 120px;">Qty Terjual</th>
                    <th class="text-right" style="width: 180px;">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produkTerlaris as $index => $produk)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $produk->nama_produk }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ number_format($produk->total_qty, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #4CAF50; font-weight: bold;">
                        Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">Tidak ada data produk terlaris pada periode ini</div>
        @endif

        <!-- Laporan Per Kasir -->
        <div class="section-title">👤 LAPORAN PER KASIR</div>
        @if($laporanPerKasir->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Kasir</th>
                    <th class="text-center" style="width: 150px;">Total Transaksi</th>
                    <th class="text-right" style="width: 180px;">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanPerKasir as $index => $kasir)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $kasir->nama_kasir }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ number_format($kasir->total_transaksi, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #9C27B0; font-weight: bold;">
                        Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">Tidak ada data kasir pada periode ini</div>
        @endif

        <!-- Detail Transaksi -->
        <div class="section-title">📋 DETAIL TRANSAKSI PENJUALAN</div>
        @if($transaksi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th class="text-center" style="width: 100px;">ID</th>
                    <th class="text-center" style="width: 130px;">Tanggal</th>
                    <th>Kasir</th>
                    <th class="text-right" style="width: 130px;">Kembalian</th>
                    <th class="text-right" style="width: 150px;">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $t->id_penjualan }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal_penjualan)->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->kasir ?? '-' }}</td>
                    <td class="text-right" style="color: #f44336;">Rp {{ number_format($t->kembalian_pembayaran ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #4CAF50; font-weight: bold;">
                        Rp {{ number_format($t->total_pembayaran ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-center">GRAND TOTAL</td>
                    <td class="text-right" style="color: #f44336;">Rp {{ number_format($transaksi->sum('kembalian_pembayaran'), 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #2196F3;">Rp {{ number_format($transaksi->sum('total_pembayaran'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="no-data">Tidak ada data transaksi pada periode ini</div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini digenerate secara otomatis oleh sistem pada {{ date('d F Y H:i:s') }} WIB</p>
            <p>Dokumen ini sah tanpa tanda tangan</p>
        </div>
    </div>
</body>
</html>
