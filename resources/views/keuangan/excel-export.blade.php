<table>
    <thead>
        <!-- Header Title -->
        <tr>
            <th colspan="6" style="text-align: center; font-size: 18px; font-weight: bold; background-color: #4CAF50; color: white; padding: 15px;">
                LAPORAN KEUANGAN
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; background-color: #f0f0f0; padding: 10px;">
                Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; background-color: #f0f0f0; padding: 5px; font-size: 11px;">
                Dicetak pada: {{ date('d F Y H:i:s') }} WIB
            </th>
        </tr>
        <tr><th colspan="6"></th></tr>
        
        <!-- Ringkasan Keuangan -->
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #2196F3; color: white; padding: 10px; font-size: 14px;">
                💰 RINGKASAN KEUANGAN
            </th>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <th colspan="3" style="text-align: left; padding: 10px; font-weight: bold;">Total Pemasukan:</th>
            <th colspan="3" style="text-align: right; padding: 10px; color: #4CAF50; font-weight: bold;">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </th>
        </tr>
        <tr style="background-color: #ffebee;">
            <th colspan="3" style="text-align: left; padding: 10px; font-weight: bold;">Total Pengeluaran:</th>
            <th colspan="3" style="text-align: right; padding: 10px; color: #f44336; font-weight: bold;">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </th>
        </tr>
        <tr style="background-color: #fff9c4;">
            <th colspan="3" style="text-align: left; padding: 10px; font-weight: bold;">Saldo Bersih:</th>
            <th colspan="3" style="text-align: right; padding: 10px; font-weight: bold; color: {{ $saldoBersih >= 0 ? '#4CAF50' : '#f44336' }};">
                Rp {{ number_format($saldoBersih, 0, ',', '.') }}
            </th>
        </tr>
        <tr style="background-color: #f5f5f5;">
            <th colspan="3" style="text-align: left; padding: 10px; font-weight: bold;">Total Transaksi:</th>
            <th colspan="3" style="text-align: right; padding: 10px; font-weight: bold;">
                {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi
            </th>
        </tr>
        <tr><th colspan="6"></th></tr>
        
        <!-- Detail Pemasukan -->
        @if($pemasukan->count() > 0)
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #4CAF50; color: white; padding: 10px; font-size: 14px;">
                📈 DETAIL PEMASUKAN PER JENIS
            </th>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <th style="text-align: center; padding: 8px; font-weight: bold;">No</th>
            <th colspan="3" style="text-align: left; padding: 8px; font-weight: bold;">Jenis Pemasukan</th>
            <th style="text-align: center; padding: 8px; font-weight: bold;">Jumlah Transaksi</th>
            <th style="text-align: right; padding: 8px; font-weight: bold;">Total</th>
        </tr>
        @foreach($pemasukan as $index => $item)
        <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
            <td style="text-align: center; padding: 8px;">{{ $index + 1 }}</td>
            <td colspan="3" style="padding: 8px;">{{ $item->jenis_keuangan }}</td>
            <td style="text-align: center; padding: 8px;">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
            <td style="text-align: right; padding: 8px; color: #4CAF50; font-weight: bold;">
                Rp {{ number_format($item->total, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr style="background-color: #c8e6c9; font-weight: bold;">
            <td colspan="4" style="text-align: center; padding: 10px; font-weight: bold;">TOTAL PEMASUKAN</td>
            <td style="text-align: center; padding: 10px; font-weight: bold;">
                {{ number_format($pemasukan->sum('jumlah'), 0, ',', '.') }}
            </td>
            <td style="text-align: right; padding: 10px; color: #2e7d32; font-weight: bold;">
                Rp {{ number_format($pemasukan->sum('total'), 0, ',', '.') }}
            </td>
        </tr>
        <tr><th colspan="6"></th></tr>
        @else
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #4CAF50; color: white; padding: 10px;">
                📈 DETAIL PEMASUKAN PER JENIS
            </th>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px; font-style: italic; color: #999;">
                Tidak ada data pemasukan pada periode ini
            </td>
        </tr>
        <tr><th colspan="6"></th></tr>
        @endif
        
        <!-- Detail Pengeluaran -->
        @if($pengeluaran->count() > 0)
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #f44336; color: white; padding: 10px; font-size: 14px;">
                📉 DETAIL PENGELUARAN PER JENIS
            </th>
        </tr>
        <tr style="background-color: #ffebee;">
            <th style="text-align: center; padding: 8px; font-weight: bold;">No</th>
            <th colspan="3" style="text-align: left; padding: 8px; font-weight: bold;">Jenis Pengeluaran</th>
            <th style="text-align: center; padding: 8px; font-weight: bold;">Jumlah Transaksi</th>
            <th style="text-align: right; padding: 8px; font-weight: bold;">Total</th>
        </tr>
        @foreach($pengeluaran as $index => $item)
        <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
            <td style="text-align: center; padding: 8px;">{{ $index + 1 }}</td>
            <td colspan="3" style="padding: 8px;">{{ $item->jenis_keuangan }}</td>
            <td style="text-align: center; padding: 8px;">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
            <td style="text-align: right; padding: 8px; color: #f44336; font-weight: bold;">
                Rp {{ number_format($item->total, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr style="background-color: #ffcdd2; font-weight: bold;">
            <td colspan="4" style="text-align: center; padding: 10px; font-weight: bold;">TOTAL PENGELUARAN</td>
            <td style="text-align: center; padding: 10px; font-weight: bold;">
                {{ number_format($pengeluaran->sum('jumlah'), 0, ',', '.') }}
            </td>
            <td style="text-align: right; padding: 10px; color: #c62828; font-weight: bold;">
                Rp {{ number_format($pengeluaran->sum('total'), 0, ',', '.') }}
            </td>
        </tr>
        <tr><th colspan="6"></th></tr>
        @else
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #f44336; color: white; padding: 10px;">
                📉 DETAIL PENGELUARAN PER JENIS
            </th>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px; font-style: italic; color: #999;">
                Tidak ada data pengeluaran pada periode ini
            </td>
        </tr>
        <tr><th colspan="6"></th></tr>
        @endif
        
        <!-- Riwayat Transaksi -->
        <tr>
            <th colspan="6" style="font-weight: bold; background-color: #FF9800; color: white; padding: 10px; font-size: 14px;">
                📋 RIWAYAT TRANSAKSI DETAIL
            </th>
        </tr>
        <tr style="background-color: #fff3e0; font-weight: bold;">
            <th style="text-align: center; padding: 8px;">No</th>
            <th style="text-align: center; padding: 8px;">Tanggal</th>
            <th style="text-align: left; padding: 8px;">Jenis</th>
            <th style="text-align: left; padding: 8px;">Keterangan</th>
            <th style="text-align: right; padding: 8px;">Nominal</th>
            <th style="text-align: center; padding: 8px;">Tipe</th>
        </tr>
    </thead>
    <tbody>
        @if($transaksi->count() > 0)
            @foreach($transaksi as $index => $t)
            <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                <td style="text-align: center; padding: 8px;">{{ $index + 1 }}</td>
                <td style="text-align: center; padding: 8px;">
                    @if($t->penjualan)
                        {{ \Carbon\Carbon::parse($t->penjualan->tanggal_penjualan)->format('d/m/Y H:i') }}
                    @elseif($t->penerimaan)
                        {{ \Carbon\Carbon::parse($t->penerimaan->tanggal_penerimaan)->format('d/m/Y H:i') }}
                    @else
                        -
                    @endif
                </td>
                <td style="padding: 8px;">{{ $t->jenis->jenis_keuangan ?? '-' }}</td>
                <td style="padding: 8px;">
                    @if($t->penjualan)
                        Penjualan #{{ $t->penjualan->id_penjualan }}
                    @elseif($t->penerimaan)
                        Penerimaan #{{ $t->penerimaan->id_penerimaan }}
                    @else
                        {{ $t->keterangan ?? '-' }}
                    @endif
                </td>
                <td style="text-align: right; padding: 8px; font-weight: bold; color: {{ $t->jenis && str_contains($t->jenis->jenis_keuangan, 'PEMASUKAN') ? '#4CAF50' : '#f44336' }};">
                    Rp {{ number_format($t->total_keuangan, 0, ',', '.') }}
                </td>
                <td style="text-align: center; padding: 8px;">
                    @if($t->jenis && str_contains($t->jenis->jenis_keuangan, 'PEMASUKAN'))
                        <span style="background-color: #4CAF50; color: white; padding: 4px 8px; border-radius: 3px; font-weight: bold;">MASUK</span>
                    @else
                        <span style="background-color: #f44336; color: white; padding: 4px 8px; border-radius: 3px; font-weight: bold;">KELUAR</span>
                    @endif
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; font-style: italic; color: #999; background-color: #f9f9f9;">
                    Tidak ada data transaksi pada periode ini
                </td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr><th colspan="6" style="border: none;"></th></tr>
        <tr>
            <th colspan="6" style="text-align: center; padding: 15px; background-color: #f5f5f5; border-top: 3px solid #4CAF50; font-size: 10px; font-style: italic;">
                Laporan ini digenerate secara otomatis oleh sistem pada {{ date('d F Y H:i:s') }} WIB<br>
                Dokumen ini sah tanpa tanda tangan
            </th>
        </tr>
    </tfoot>
</table>
