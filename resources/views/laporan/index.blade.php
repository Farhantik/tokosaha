@extends('layouts.app')

@section('title', 'Laporan Penjualan - Toko Sahabat')
@section('page-title', 'Laporan Penjualan')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-chart-bar text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Laporan Penjualan</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Analisis penjualan toko</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="{{ route('laporan.export.pdf') }}?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                    <i class="fas fa-file-pdf mr-2"></i>Ekspor PDF
                </a>
                <a href="{{ route('laporan.export.excel') }}?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                    <i class="fas fa-file-excel mr-2"></i>Ekspor Excel
                </a>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <form method="GET" action="{{ route('laporan.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm text-gray-700 font-semibold mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex flex-col sm:flex-row gap-3 lg:items-end">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('laporan.index') }}" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-2.5 px-4 rounded-xl text-center transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Empty State Alert -->
        @if ($transaksi->isEmpty() && $totalPenjualan == 0)
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 sm:p-6 rounded-xl shadow-md">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-yellow-800">Tidak Ada Data</h3>
                        <p class="text-xs sm:text-sm text-yellow-700 mt-1">
                            Belum ada transaksi pada periode <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</strong>
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('kasir.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white rounded-xl transition-all text-sm font-semibold shadow-md">
                                <i class="fas fa-cash-register mr-2"></i>Buat Transaksi Pertama
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="text-blue-100 text-xs sm:text-sm mb-1 font-medium">Total Penjualan</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold truncate">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-blue-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs sm:text-sm mb-1 font-medium">Total Transaksi</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold">{{ $totalTransaksi }}</h3>
                    </div>
                    <div class="bg-green-400 bg-opacity-50 rounded-xl p-3 sm:p-4">
                        <i class="fas fa-shopping-cart text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="text-purple-100 text-xs sm:text-sm mb-1 font-medium">Total Kembalian</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold truncate">Rp {{ number_format($totalKembalian, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-purple-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-exchange-alt text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Penjualan Per Hari -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                <span>Grafik Penjualan</span>
            </h2>
            <div class="relative h-[300px] sm:h-[350px] lg:h-[400px]">
                <canvas id="chartPenjualan"></canvas>
            </div>
        </div>

        <!-- Produk Terlaris & Laporan Per Kasir -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Produk Terlaris -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
                <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                    <span>Produk Terlaris</span>
                </h2>
                <div class="block lg:hidden space-y-3">
                    @forelse ($produkTerlaris as $produk)
                        <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900 text-sm flex-1 truncate">{{ $produk->nama_produk }}</span>
                                <span class="ml-2 bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap">{{ $produk->total_qty }}x</span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-bold text-emerald-600">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data produk</div>
                    @endforelse
                </div>
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Produk</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Terjual</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($produkTerlaris as $produk)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm"><span class="font-semibold text-gray-900">{{ $produk->nama_produk }}</span></td>
                                    <td class="px-4 py-3 text-sm text-center"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold text-xs">{{ $produk->total_qty }}</span></td>
                                    <td class="px-4 py-3 text-sm text-right font-bold">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada data produk</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Laporan Per Kasir -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
                <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-green-600"></i>
                    <span>Laporan Per Kasir</span>
                </h2>
                <div class="block lg:hidden space-y-3">
                    @forelse ($laporanPerKasir as $kasir)
                        <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900 text-sm flex-1 truncate">{{ $kasir->nama_kasir }}</span>
                                <span class="ml-2 bg-green-100 text-green-800 px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap">{{ $kasir->total_transaksi }}x</span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-bold text-emerald-600">Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data kasir</div>
                    @endforelse
                </div>
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Transaksi</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($laporanPerKasir as $kasir)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $kasir->nama_kasir }}</td>
                                    <td class="px-4 py-3 text-sm text-center"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold text-xs">{{ $kasir->total_transaksi }}</span></td>
                                    <td class="px-4 py-3 text-sm text-right font-bold">Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada data kasir</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Laporan Detail Produk Terjual -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                <h2 class="text-base sm:text-xl font-bold flex items-center">
                    <i class="fas fa-box mr-2 text-indigo-600"></i>
                    <span>Laporan Detail Produk Terjual</span>
                </h2>
                <button onclick="exportTableToExcel('tabelProduk', 'Laporan_Produk_Terjual')" class="px-3 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg text-xs font-semibold transition-all">
                    <i class="fas fa-file-excel mr-1"></i>Export Excel
                </button>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200">
                    <p class="text-xs text-blue-600 font-semibold mb-1">Total Produk Terjual</p>
                    <p class="text-lg sm:text-xl font-bold text-blue-700">{{ $detailProduk->count() }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-3 border border-green-200">
                    <p class="text-xs text-green-600 font-semibold mb-1">Total Qty Terjual</p>
                    <p class="text-lg sm:text-xl font-bold text-green-700">{{ number_format($detailProduk->sum('total_qty'), 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-3 border border-purple-200">
                    <p class="text-xs text-purple-600 font-semibold mb-1">Total Omzet</p>
                    <p class="text-base sm:text-lg font-bold text-purple-700">Rp {{ number_format($detailProduk->sum('total_penjualan'), 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-3 border border-orange-200">
                    <p class="text-xs text-orange-600 font-semibold mb-1">Rata-rata/Produk</p>
                    <p class="text-base sm:text-lg font-bold text-orange-700">Rp {{ $detailProduk->count() > 0 ? number_format($detailProduk->sum('total_penjualan') / $detailProduk->count(), 0, ',', '.') : 0 }}</p>
                </div>
            </div>

            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchProduk" class="w-full pl-11 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Cari produk...">
                </div>
                <div class="relative">
                    <i class="fas fa-filter absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <select id="filterKategori" class="w-full pl-11 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm appearance-none bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->id_produk_kategori }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
            </div>

            <!-- Desktop View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="tabelProduk">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase w-10"></th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">NO</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">KODE</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">PRODUK</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">QTY</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">TRX</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">HARGA</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">TOTAL</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">AVG</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($detailProduk as $index => $produk)
                            <tr class="produk-row hover:bg-indigo-50 transition-colors" data-kategori="{{ $produk->id_produk_kategori ?? '' }}">
                                <td class="px-4 py-3 text-center">
                                    <button onclick="toggleDetail({{ $index }})" class="w-6 h-6 rounded-full bg-indigo-100 hover:bg-indigo-200 text-indigo-600 flex items-center justify-center transition-all">
                                        <i class="fas fa-chevron-right text-xs transition-transform duration-300" id="icon-{{ $index }}"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-semibold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm"><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $produk->code_produk }}</span></td>
                                <td class="px-4 py-3 text-sm">
                                    <div>
                                        <span class="font-semibold text-gray-900">{{ $produk->nama_produk }}</span>
                                        @if($produk->nama_kategori)
                                            <span class="block text-xs text-gray-500 mt-0.5">
                                                <i class="fas fa-tag text-xs mr-1"></i>{{ $produk->nama_kategori }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-center"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold">{{ number_format($produk->total_qty, 0, ',', '.') }}</span></td>
                                <td class="px-4 py-3 text-sm text-center"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold">{{ $produk->total_transaksi }}</span></td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-700">Rp {{ number_format($produk->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-indigo-600">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ number_format($produk->rata_rata_qty, 1) }}</td>
                            </tr>
                            <tr id="detail-{{ $index }}" class="hidden bg-indigo-50">
                                <td colspan="9" class="px-4 py-4">
                                    <div class="bg-white rounded-lg shadow-sm p-4">
                                        <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                            <i class="fas fa-list-ul mr-2 text-indigo-600"></i>
                                            Detail Transaksi - {{ $produk->nama_produk }} ({{ $produk->total_transaksi }} transaksi)
                                        </h4>
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">No. Transaksi</th>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kasir</th>
                                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600">Qty</th>
                                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Harga</th>
                                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-100">
                                                @foreach ($produk->detailTransaksi as $detail)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-3 py-2 text-xs"><span class="font-mono font-semibold text-blue-600">#{{ str_pad($detail->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span></td>
                                                        <td class="px-3 py-2 text-xs text-gray-600">{{ \Carbon\Carbon::parse($detail->tanggal_penjualan)->format('d/m/Y H:i') }}</td>
                                                        <td class="px-3 py-2 text-xs text-gray-700"><i class="fas fa-user text-xs mr-1 text-gray-400"></i>{{ $detail->nama_kasir ?? 'Admin' }}</td>
                                                        <td class="px-3 py-2 text-xs text-center"><span class="bg-blue-50 text-blue-700 px-2 py-1 rounded font-semibold">{{ $detail->qty }} pcs</span></td>
                                                        <td class="px-3 py-2 text-xs text-right text-gray-600">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                                                        <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-3 py-2 text-xs font-bold text-gray-700">Total</td>
                                                <td class="px-3 py-2 text-xs text-center"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold">{{ number_format($produk->total_qty, 0, ',', '.') }} pcs</span></td>
                                                <td class="px-3 py-2 text-xs text-right text-gray-600">-</td>
                                                <td class="px-3 py-2 text-xs text-right font-bold text-indigo-600">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-12 text-center text-gray-500">Tidak ada data produk terjual</td></tr>
                    @endforelse
                </tbody>
                @if($detailProduk->count() > 0)
                <tfoot class="bg-indigo-50 border-t-2 border-indigo-200">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-800">TOTAL</td>
                        <td class="px-4 py-3 text-sm text-center"><span class="bg-blue-200 text-blue-900 px-3 py-1 rounded-full font-bold">{{ number_format($detailProduk->sum('total_qty'), 0, ',', '.') }}</span></td>
                        <td class="px-4 py-3 text-sm text-center"><span class="bg-green-200 text-green-900 px-3 py-1 rounded-full font-bold">{{ number_format($detailProduk->sum('total_transaksi'), 0, ',', '.') }}</span></td>
                        <td class="px-4 py-3 text-sm text-right">-</td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-indigo-700">Rp {{ number_format($detailProduk->sum('total_penjualan'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-center">-</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Mobile View -->
        <div class="block lg:hidden space-y-3" id="produkCardList">
            @forelse ($detailProduk as $index => $produk)
                <div class="produk-card bg-gradient-to-r from-gray-50 to-white border-l-4 border-indigo-500 rounded-lg shadow-sm" data-kategori="{{ $produk->id_produk_kategori ?? '' }}">
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0 mr-2">
                                <h3 class="font-bold text-gray-900 text-sm truncate mb-1">{{ $produk->nama_produk }}</h3>
                                <p class="text-xs text-gray-500">{{ $produk->code_produk }}</p>
                                @if($produk->nama_kategori)
                                    <p class="text-xs text-indigo-600 mt-1">
                                        <i class="fas fa-tag text-xs mr-1"></i>{{ $produk->nama_kategori }}
                                    </p>
                                @endif
                            </div>
                            <span class="flex-shrink-0 bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-full text-xs font-bold">#{{ $index + 1 }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-blue-50 rounded-lg p-2">
                                <p class="text-xs text-blue-600 font-medium mb-1">Terjual</p>
                                <p class="text-lg font-bold text-blue-700">{{ number_format($produk->total_qty, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-2">
                                <p class="text-xs text-green-600 font-medium mb-1">Transaksi</p>
                                <p class="text-lg font-bold text-green-700">{{ $produk->total_transaksi }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600">Harga Satuan:</span>
                                <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($produk->harga_satuan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600">Total Penjualan:</span>
                                <span class="text-base font-bold text-indigo-600">Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600">Rata-rata/Transaksi:</span>
                                <span class="text-sm font-medium text-gray-700">{{ number_format($produk->rata_rata_qty, 1) }} pcs</span>
                            </div>
                        </div>
                        <button onclick="toggleDetailMobile({{ $index }})" class="w-full mt-3 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center justify-center">
                            <i class="fas fa-chevron-down mr-2 transition-transform duration-300" id="icon-mobile-{{ $index }}"></i>
                            <span id="text-mobile-{{ $index }}">Lihat Detail Transaksi</span>
                        </button>
                    </div>
                    <div id="detail-mobile-{{ $index }}" class="hidden border-t border-gray-200 bg-gray-50 p-3">
                        <h4 class="text-xs font-bold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-list-ul mr-2"></i>
                            Detail Transaksi ({{ $produk->total_transaksi }}x)
                        </h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach ($produk->detailTransaksi as $detail)
                                <div class="bg-white rounded-lg p-2 border border-gray-200">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs font-mono font-semibold text-blue-600">#{{ str_pad($detail->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($detail->tanggal_penjualan)->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-600">Qty: <strong>{{ $detail->qty }}</strong> pcs</span>
                                        <span class="text-xs font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-user text-xs mr-1"></i>{{ $detail->nama_kasir ?? 'Admin' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-box-open text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">Tidak ada data produk terjual</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
        <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-history mr-2 text-gray-600"></i>
            <span>Riwayat Transaksi</span>
        </h2>
        <div class="block lg:hidden space-y-3">
            @forelse ($transaksi as $item)
                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-sm font-bold text-blue-600">#{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
                        <a href="{{ route('transaksi.struk', $item->id_penjualan) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                            <i class="fas fa-receipt mr-1"></i>Struk
                        </a>
                    </div>
                    <div class="space-y-2 text-xs sm:text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Tanggal:</span><span class="font-medium">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Kasir:</span><span class="font-medium">{{ $item->kasir ?? 'Admin' }}</span></div>
                        <div class="flex justify-between pt-2 border-t"><span class="text-gray-600">Total:</span><span class="font-bold text-blue-600">Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Bayar:</span><span class="font-medium">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Kembalian:</span><span class="font-semibold text-green-600">Rp {{ number_format($item->kembalian_pembayaran, 0, ',', '.') }}</span></div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 text-sm">Tidak ada transaksi pada periode ini</div>
            @endforelse
            @if ($transaksi->hasPages())
                <div class="mt-4">{{ $transaksi->links() }}</div>
            @endif
        </div>
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">No. Transaksi</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Kasir</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Bayar</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Kembalian</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transaksi as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-semibold">#{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->kasir ?? 'Admin' }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold">Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right text-green-600 font-semibold">Rp {{ number_format($item->kembalian_pembayaran, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('transaksi.struk', $item->id_penjualan) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition">
                                    <i class="fas fa-receipt mr-1"></i>Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi pada periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($transaksi->hasPages())
                <div class="mt-4">{{ $transaksi->links() }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function toggleDetail(index) {
            const detailRow = document.getElementById('detail-' + index);
            const icon = document.getElementById('icon-' + index);
            if (detailRow.classList.contains('hidden')) {
                detailRow.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                detailRow.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleDetailMobile(index) {
            const detailDiv = document.getElementById('detail-mobile-' + index);
            const icon = document.getElementById('icon-mobile-' + index);
            const text = document.getElementById('text-mobile-' + index);
            if (detailDiv.classList.contains('hidden')) {
                detailDiv.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
                text.textContent = 'Sembunyikan Detail';
            } else {
                detailDiv.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
                text.textContent = 'Lihat Detail Transaksi';
            }
        }

        const labels = @json($penjualanPerHari->pluck('tanggal'));
        const dataPenjualan = @json($penjualanPerHari->pluck('total_penjualan'));
        const dataTransaksi = @json($penjualanPerHari->pluck('total_transaksi'));

        const formatLabels = labels.map(label => {
            const date = new Date(label);
            const isMobile = window.innerWidth < 640;
            return isMobile ? date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short'}) : date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
        });

        const ctx = document.getElementById('chartPenjualan').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: formatLabels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: dataPenjualan,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    yAxisID: 'y',
                    borderRadius: 8,
                    borderSkipped: false
                }, {
                    label: 'Transaksi',
                    data: dataTransaksi,
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 2,
                    yAxisID: 'y1',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {mode: 'index', intersect: false},
                scales: {
                    x: {grid: {display: false}, ticks: {font: {size: window.innerWidth < 640 ? 10 : 12}, maxRotation: window.innerWidth < 640 ? 45 : 0, minRotation: window.innerWidth < 640 ? 45 : 0}},
                    y: {type: 'linear', display: true, position: 'left', grid: {color: 'rgba(0, 0, 0, 0.05)'}, ticks: {font: {size: window.innerWidth < 640 ? 10 : 12}, callback: function(value) {
                        if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        else if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }}},
                    y1: {type: 'linear', display: true, position: 'right', grid: {drawOnChartArea: false}, ticks: {font: {size: window.innerWidth < 640 ? 10 : 12}, stepSize: 1}}
                },
                plugins: {
                    tooltip: {backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, titleFont: {size: 14, weight: 'bold'}, bodyFont: {size: 13}, callbacks: {label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) {
                            if (context.datasetIndex === 0) label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            else label += context.parsed.y + ' transaksi';
                        }
                        return label;
                    }}},
                    legend: {display: true, position: window.innerWidth < 640 ? 'bottom' : 'top', labels: {padding: window.innerWidth < 640 ? 10 : 15, font: {size: window.innerWidth < 640 ? 11 : 13}, usePointStyle: true, pointStyle: 'circle'}}
                }
            }
        });

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const isMobile = window.innerWidth < 640;
                chart.data.labels = labels.map(label => {
                    const date = new Date(label);
                    return isMobile ? date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short'}) : date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
                });
                chart.options.scales.x.ticks.font.size = isMobile ? 10 : 12;
                chart.options.scales.x.ticks.maxRotation = isMobile ? 45 : 0;
                chart.options.scales.x.ticks.minRotation = isMobile ? 45 : 0;
                chart.options.scales.y.ticks.font.size = isMobile ? 10 : 12;
                chart.options.scales.y1.ticks.font.size = isMobile ? 10 : 12;
                chart.options.plugins.legend.position = isMobile ? 'bottom' : 'top';
                chart.options.plugins.legend.labels.padding = isMobile ? 10 : 15;
                chart.options.plugins.legend.labels.font.size = isMobile ? 11 : 13;
                chart.update();
            }, 250);
        });

        document.getElementById('searchProduk')?.addEventListener('input', function() {
            filterProduk();
        });

        document.getElementById('filterKategori')?.addEventListener('change', function() {
            filterProduk();
        });

        function filterProduk() {
            const searchValue = document.getElementById('searchProduk').value.toLowerCase();
            const kategoriValue = document.getElementById('filterKategori').value;

            // Filter Mobile Cards
            document.querySelectorAll('.produk-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                const kategori = card.getAttribute('data-kategori');
                
                const matchSearch = text.includes(searchValue);
                const matchKategori = !kategoriValue || kategori === kategoriValue;
                
                card.style.display = (matchSearch && matchKategori) ? '' : 'none';
            });

            // Filter Desktop Table Rows
            document.querySelectorAll('.produk-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                const kategori = row.getAttribute('data-kategori');
                const nextRow = row.nextElementSibling;
                
                const matchSearch = text.includes(searchValue);
                const matchKategori = !kategoriValue || kategori === kategoriValue;
                
                row.style.display = (matchSearch && matchKategori) ? '' : 'none';
                
                // Hide detail row if parent is hidden
                if (nextRow && nextRow.id && nextRow.id.startsWith('detail-')) {
                    if (!matchSearch || !matchKategori) {
                        nextRow.style.display = 'none';
                    }
                }
            });

            // Update visible count
            updateVisibleCount();
        }

        function updateVisibleCount() {
            const visibleDesktop = Array.from(document.querySelectorAll('.produk-row')).filter(row => row.style.display !== 'none').length;
            const visibleMobile = Array.from(document.querySelectorAll('.produk-card')).filter(card => card.style.display !== 'none').length;
            
            // You can add a counter display here if needed
            console.log('Visible products:', Math.max(visibleDesktop, visibleMobile));
        }

        function exportTableToExcel(tableID, filename = '') {
            const table = document.getElementById(tableID);
            const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
            XLSX.writeFile(wb, filename + '.xlsx');
        }
    </script>
@endpush
@endsection