@extends('layouts.app')

@section('title', 'Laporan Penjualan - Toko Sahabat')
@section('page-title', 'Laporan Penjualan')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-chart-bar text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Laporan Penjualan</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Analisis penjualan toko</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="{{ route('laporan.export.pdf') }}?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}"
                    target="_blank"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Ekspor PDF
                </a>
                <a href="{{ route('laporan.export.excel') }}?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                    <i class="fas fa-file-excel mr-2"></i>
                    Ekspor Excel
                </a>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <form method="GET" action="{{ route('laporan.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm text-gray-700 font-semibold mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex flex-col sm:flex-row gap-3 lg:items-end">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('laporan.index') }}"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-2.5 px-4 rounded-xl text-center transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Empty State Alert -->
        @if ($transaksi->isEmpty() && $totalPenjualan == 0)
            <div
                class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 sm:p-6 rounded-xl shadow-md">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-yellow-800">Tidak Ada Data</h3>
                        <p class="text-xs sm:text-sm text-yellow-700 mt-1">
                            Belum ada transaksi pada periode
                            <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }}</strong> sampai
                            <strong>{{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</strong>
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('kasir.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white rounded-xl transition-all text-sm font-semibold shadow-md">
                                <i class="fas fa-cash-register mr-2"></i>
                                Buat Transaksi Pertama
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="text-blue-100 text-xs sm:text-sm mb-1 font-medium">Total Penjualan</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold truncate">
                            Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-blue-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
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

            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="text-purple-100 text-xs sm:text-sm mb-1 font-medium">Total Kembalian</p>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold truncate">
                            Rp {{ number_format($totalKembalian, 0, ',', '.') }}
                        </h3>
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

                <!-- Mobile: Card View -->
                <div class="block lg:hidden space-y-3">
                    @forelse ($produkTerlaris as $index => $produk)
                        <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="font-semibold text-gray-900 text-sm flex-1 truncate">{{ $produk->nama_produk }}</span>
                                <span
                                    class="ml-2 bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                    {{ $produk->total_qty }}x
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-bold text-emerald-600">
                                    Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data produk</div>
                    @endforelse
                </div>

                <!-- Desktop: Table View -->
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
                            @forelse ($produkTerlaris as $index => $produk)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-gray-900">{{ $produk->nama_produk }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold text-xs">
                                            {{ $produk->total_qty }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold">
                                        Rp {{ number_format($produk->total_penjualan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        Tidak ada data produk
                                    </td>
                                </tr>
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

                <!-- Mobile: Card View -->
                <div class="block lg:hidden space-y-3">
                    @forelse ($laporanPerKasir as $kasir)
                        <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="font-semibold text-gray-900 text-sm flex-1 truncate">{{ $kasir->nama_kasir }}</span>
                                <span
                                    class="ml-2 bg-green-100 text-green-800 px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                    {{ $kasir->total_transaksi }}x
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-bold text-emerald-600">
                                    Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data kasir</div>
                    @endforelse
                </div>

                <!-- Desktop: Table View -->
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
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold text-xs">
                                            {{ $kasir->total_transaksi }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold">
                                        Rp {{ number_format($kasir->total_penjualan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        Tidak ada data kasir
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-history mr-2 text-gray-600"></i>
                <span>Riwayat Transaksi</span>
            </h2>

            <!-- Mobile: Card View -->
            <div class="block lg:hidden space-y-3">
                @forelse ($transaksi as $item)
                    <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-mono text-sm font-bold text-blue-600">
                                #{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}
                            </span>
                            <a href="{{ route('transaksi.struk', $item->id_penjualan) }}" target="_blank"
                                class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                                <i class="fas fa-receipt mr-1"></i>Struk
                            </a>
                        </div>
                        <div class="space-y-2 text-xs sm:text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span
                                    class="font-medium">{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kasir:</span>
                                <span class="font-medium">{{ $item->kasir ?? 'Admin' }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-bold text-blue-600">Rp
                                    {{ number_format($item->total_pembayaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bayar:</span>
                                <span class="font-medium">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kembalian:</span>
                                <span class="font-semibold text-green-600">Rp
                                    {{ number_format($item->kembalian_pembayaran, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm">Tidak ada transaksi pada periode ini</div>
                @endforelse

                <!-- Mobile Pagination -->
                @if ($transaksi->hasPages())
                    <div class="mt-4">
                        {{ $transaksi->links() }}
                    </div>
                @endif
            </div>

            <!-- Desktop: Table View -->
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
                                <td class="px-4 py-3 text-sm font-semibold">
                                    #{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $item->kasir ?? 'Admin' }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">
                                    Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    Rp {{ number_format($item->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-green-600 font-semibold">
                                    Rp {{ number_format($item->kembalian_pembayaran, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('transaksi.struk', $item->id_penjualan) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition">
                                        <i class="fas fa-receipt mr-1"></i>Struk
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada transaksi pada periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Desktop Pagination -->
                @if ($transaksi->hasPages())
                    <div class="mt-4">
                        {{ $transaksi->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Data untuk chart
            const labels = @json($penjualanPerHari->pluck('tanggal'));
            const dataPenjualan = @json($penjualanPerHari->pluck('total_penjualan'));
            const dataTransaksi = @json($penjualanPerHari->pluck('total_transaksi'));

            // Format tanggal untuk mobile
            const formatLabels = labels.map(label => {
                const date = new Date(label);
                const isMobile = window.innerWidth < 640;
                return isMobile ?
                    date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short'
                    }) :
                    date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
            });

            // Create chart
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
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                },
                                maxRotation: window.innerWidth < 640 ? 45 : 0,
                                minRotation: window.innerWidth < 640 ? 45 : 0
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                font: {
                                    size: window.innerWidth < 640 ? 10 : 12
                                },
                                stepSize: 1
                            }
                        },
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.datasetIndex === 0) {
                                            label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        } else {
                                            label += context.parsed.y + ' transaksi';
                                        }
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: window.innerWidth < 640 ? 'bottom' : 'top',
                            labels: {
                                padding: window.innerWidth < 640 ? 10 : 15,
                                font: {
                                    size: window.innerWidth < 640 ? 11 : 13
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });

            // Update chart on window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const isMobile = window.innerWidth < 640;

                    // Update labels
                    chart.data.labels = labels.map(label => {
                        const date = new Date(label);
                        return isMobile ?
                            date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short'
                            }) :
                            date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                    });

                    // Update options
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
        </script>
    @endpush
@endsection
