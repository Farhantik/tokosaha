<table>
    <thead>
        <!-- Header Title -->
        <tr>
            <th colspan="7" style="text-align: center; font-size: 18px; font-weight: bold; background-color: #2196F3; color: white; padding: 15px;">
                LAPORAN PENJUALAN
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; background-color: #f0f0f0; padding: 10px;">
                Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; background-color: #f0f0f0; padding: 5px; font-size: 11px;">
                Dicetak pada: {{ date('d F Y H:i:s') }} WIB
            </th>
        </tr>
        <tr><th colspan="7"></th></tr>
        
        <!-- Ringkasan Penjualan -->
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #FF9800; color: white; padding: 10px; font-size: 14px;">
                💰 RINGKASAN PENJUALAN
            </th>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <th colspan="4" style="text-align: left; padding: 10px; font-weight: bold;">Total Penjualan:</th>
            <th colspan="3" style="text-align: right; padding: 10px; color: #4CAF50; font-weight: bold;">
                Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
            </th>
        </tr>
        <tr style="background-color: #fff9c4;">
            <th colspan="4" style="text-align: left; padding: 10px; font-weight: bold;">Total Transaksi:</th>
            <th colspan="3" style="text-align: right; padding: 10px; font-weight: bold;">
                {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi
            </th>
        </tr>
        <tr style="background-color: #ffebee;">
            <th colspan="4" style="text-align: left; padding: 10px; font-weight: bold;">Total Kembalian:</th>
            <th colspan="3" style="text-align: right; padding: 10px; color: #f44336; font-weight: bold;">
                Rp {{ number_format($totalKembalian, 0, ',', '.') }}
            </th>
        </tr>
        <tr style="background-color: #e3f2fd;">
            <th colspan="4" style="text-align: left; padding: 10px; font-weight: bold;">Penjualan Bersih:</th>
            <th colspan="3" style="text-align: right; padding: 10px; color: #2196F3; font-weight: bold;">
                Rp {{ number_format($totalPenjualan - $totalKembalian, 0, ',', '.') }}
            </th>
        </tr>
        <tr><th colspan="7"></th></tr>
        
        <!-- Produk Terlaris -->
        @if($produkTerlaris->count() > 0)
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #4CAF50; color: white; padding: 10px; font-size: 14px;">
                🏆 TOP 10 PRODUK TERLARIS
            </th>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <th style="text-align: center; padding: 8px; font-weight: bold;">No</th>
            <th colspan="4" style="text-align: left; padding: 8px; font-weight: bold;">Nama Produk</th>
            <th style="text-align: center; padding: 8px; font-weight: bold;">Qty Terjual</th>
            <th style="text-align: right; padding: 8px; font-weight: bold;">Total Penjualan</th>
        </tr>
        @foreach($produkTerlaris as $index => $produk)
        <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
            <td style="text-align: center; padding: 8px;">{{ $index + 1 }}</td>
            <td colspan="4" style="padding: 8px;">{{ $produk->nama_produk }}</td>
            <td style="text-align: center; padding: 8px; font-weight: bold;">
                {{ number_format($produk->total_qty, 0, ',', '.') }}
            </td>
            <td style="text-align: right; padding: 8px; color: #4CAF50; font-weight: bold;">
                Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr><th colspan="7"></th></tr>
        @else
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #4CAF50; color: white; padding: 10px;">
                🏆 TOP 10 PRODUK TERLARIS
            </th>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; padding: 20px; font-style: italic; color: #999;">
                Tidak ada data produk terlaris
            </td>
        </tr>
        <tr><th colspan="7"></th></tr>
        @endif
        
        <!-- Laporan Per Kasir -->
        @if($laporanPerKasir->count() > 0)
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #9C27B0; color: white; padding: 10px; font-size: 14px;">
                👤 LAPORAN PER KASIR
            </th>
        </tr>
        <tr style="background-color: #f3e5f5;">
            <th style="text-align: center; padding: 8px; font-weight: bold;">No</th>
            <th colspan="4" style="text-align: left; padding: 8px; font-weight: bold;">Nama Kasir</th>
            <th style="text-align: center; padding: 8px; font-weight: bold;">Total Transaksi</th>
            <th style="text-align: right; padding: 8px; font-weight: bold;">Total Penjualan</th>
        </tr>
        @foreach($laporanPerKasir as $index => $kasir)
        <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
            <td style="text-align: center; padding: 8px;">{{ $index + 1 }}</td>
            <td colspan="4" style="padding: 8px;">{{ $kasir->nama_kasir }}</td>
            <td style="text-align: center; padding: 8px; font-weight: bold;">
                {{ number_format($kasir->total_transaksi, 0, ',', '.') }}
            </td>
            <td style="text-align: right; padding: 8px; color: #9C27B0; font-weight: bold;">
                Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr><th colspan="7"></th></tr>
        @else
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #9C27B0; color: white; padding: 10px;">
                👤 LAPORAN PER KASIR
            </th>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; padding: 20px; font-style: italic; color: #999;">
                Tidak ada data kasir
            </td>
        </tr>
        <tr><th colspan="7"></th></tr>
        @endif
        
        <!-- Detail Transaksi -->
        <tr>
            <th colspan="7" style="font-weight: bold; background-color: #2196F3; color: white; padding: 10px; font-size: 14px;">
                📋 DETAIL TRANSAKSI PENJUALAN
            </th>
        </tr>
        <tr style="background-color: #e3f2fd; font-weight: bold;">
            <th style="text-align: center; padding: 8px;">No</th>
            <th style="text-align: center; padding: 8px;">ID Penjualan</th>
            <th style="text-align: center; padding: 8px;">Tanggal</th>
            <th colspan="2" style="text-align: left; padding: 8px;">Kasir</th>
            <th style="text-align: right; padding: 8px;">Kembalian</th>
            <th style="text-align: right; padding: 8px;">Total Bayar</th>
        </tr>
    </thead>
    <tbody>
        @if($transaksi->count() > 0)
            @foreach($transaksi as $index => $t)
            <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                <td style="text-align: center; padding: 8px;">{{ $index + 1 }}</td>
                <td style="text-align: center; padding: 8px;">{{ $t->id_penjualan }}</td>
                <td style="text-align: center; padding: 8px;">
                    {{ \Carbon\Carbon::parse($t->tanggal_penjualan)->format('d/m/Y H:i') }}
                </td>
                <td colspan="2" style="padding: 8px;">{{ $t->kasir ?? '-' }}</td>
                <td style="text-align: right; padding: 8px; color: #f44336;">
                    Rp {{ number_format($t->kembalian_pembayaran ?? 0, 0, ',', '.') }}
                </td>
                <td style="text-align: right; padding: 8px; color: #4CAF50; font-weight: bold;">
                    Rp {{ number_format($t->total_pembayaran ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            
            <!-- Total Row -->
            <tr style="background-color: #bbdefb; font-weight: bold;">
                <td colspan="5" style="text-align: center; padding: 10px; font-weight: bold;">
                    GRAND TOTAL
                </td>
                <td style="text-align: right; padding: 10px; color: #f44336; font-weight: bold;">
                    Rp {{ number_format($transaksi->sum('kembalian_pembayaran'), 0, ',', '.') }}
                </td>
                <td style="text-align: right; padding: 10px; color: #1976D2; font-weight: bold;">
                    Rp {{ number_format($transaksi->sum('total_pembayaran'), 0, ',', '.') }}
                </td>
            </tr>
        @else
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; font-style: italic; color: #999; background-color: #f9f9f9;">
                    Tidak ada data transaksi pada periode ini
                </td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr><th colspan="7" style="border: none;"></th></tr>
        <tr>
            <th colspan="7" style="text-align: center; padding: 15px; background-color: #f5f5f5; border-top: 3px solid #2196F3; font-size: 10px; font-style: italic;">
                Laporan ini digenerate secara otomatis oleh sistem pada {{ date('d F Y H:i:s') }} WIB<br>
                Dokumen ini sah tanpa tanda tangan
            </th>
        </tr>
    </tfoot>
</table>
