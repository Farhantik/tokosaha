@extends('layouts.app')

@section('title', 'Laporan Keuangan - Toko Sahabat')
@section('page-title', 'Laporan Keuangan')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-file-invoice-dollar text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Laporan Keuangan</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Ringkasan pemasukan dan pengeluaran</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="{{ route('keuangan.export.pdf') }}?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}"
                    target="_blank"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Ekspor PDF
                </a>
                <a href="{{ route('keuangan.export.excel') }}?tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all text-sm sm:text-base">
                    <i class="fas fa-file-excel mr-2"></i>
                    Ekspor Excel
                </a>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <form method="GET" action="{{ route('keuangan.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm text-gray-700 font-semibold mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex flex-col sm:flex-row gap-3 lg:items-end">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('keuangan.index') }}"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-2.5 px-4 rounded-xl text-center transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Ringkasan Keuangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Pemasukan -->
            <div
                class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <p class="text-emerald-100 text-xs sm:text-sm mb-1 font-medium">Total Pemasukan</p>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold">
                            Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-emerald-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-arrow-up text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div
                class="bg-gradient-to-br from-red-500 to-pink-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <p class="text-red-100 text-xs sm:text-sm mb-1 font-medium">Total Pengeluaran</p>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold">
                            Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-red-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-arrow-down text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Saldo Bersih -->
            <div
                class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <p class="text-blue-100 text-xs sm:text-sm mb-1 font-medium">Saldo Bersih</p>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold">
                            Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-blue-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-wallet text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div
                class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <p class="text-purple-100 text-xs sm:text-sm mb-1 font-medium">Total Transaksi</p>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold">
                            {{ number_format($totalTransaksi) }}
                        </h3>
                    </div>
                    <div class="bg-purple-400 bg-opacity-50 rounded-xl p-3 sm:p-4 flex-shrink-0">
                        <i class="fas fa-exchange-alt text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Keuangan -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-chart-area mr-2 text-emerald-600"></i>
                <span>Grafik Keuangan</span>
            </h2>
            <div class="relative h-[300px] sm:h-[350px] lg:h-[400px]">
                <canvas id="chartKeuangan"></canvas>
            </div>
        </div>

        <!-- Ringkasan Per Jenis Keuangan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Pemasukan -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
                <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>
                    <span>Detail Pemasukan</span>
                </h2>

                <!-- Mobile: Card View -->
                <div class="block lg:hidden space-y-3">
                    @forelse ($pemasukan as $item)
                        <div class="bg-gradient-to-r from-emerald-50 to-white border border-emerald-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900 text-sm flex-1 truncate">
                                    {{ $item->jenis_keuangan }}
                                </span>
                                <span
                                    class="ml-2 bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                    {{ $item->jumlah }}x
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-bold text-emerald-600">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data pemasukan</div>
                    @endforelse
                </div>

                <!-- Desktop: Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-emerald-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Jenis</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pemasukan as $item)
                                <tr class="hover:bg-emerald-50">
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $item->jenis_keuangan }}</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span
                                            class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-bold text-xs">
                                            {{ $item->jumlah }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-emerald-600">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        Tidak ada data pemasukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
                <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-minus-circle mr-2 text-red-600"></i>
                    <span>Detail Pengeluaran</span>
                </h2>

                <!-- Mobile: Card View -->
                <div class="block lg:hidden space-y-3">
                    @forelse ($pengeluaran as $item)
                        <div class="bg-gradient-to-r from-red-50 to-white border border-red-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900 text-sm flex-1 truncate">
                                    {{ $item->jenis_keuangan }}
                                </span>
                                <span
                                    class="ml-2 bg-red-100 text-red-800 px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                    {{ $item->jumlah }}x
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-bold text-red-600">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data pengeluaran</div>
                    @endforelse
                </div>

                <!-- Desktop: Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Jenis</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pengeluaran as $item)
                                <tr class="hover:bg-red-50">
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $item->jenis_keuangan }}</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full font-bold text-xs">
                                            {{ $item->jumlah }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-red-600">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        Tidak ada data pengeluaran
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi Keuangan -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-history mr-2 text-gray-600"></i>
                <span>Riwayat Transaksi Keuangan</span>
            </h2>

            <!-- Mobile: Card View -->
            <div class="block lg:hidden space-y-3">
                @forelse ($transaksi as $item)
                    <div
                        class="bg-gradient-to-r from-gray-50 to-white border rounded-lg p-4
                        {{ strpos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false ? 'border-emerald-200' : 'border-red-200' }}">
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="font-mono text-sm font-bold
                                {{ strpos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false ? 'text-emerald-600' : 'text-red-600' }}">
                                #{{ str_pad($item->id_keuangan, 6, '0', STR_PAD_LEFT) }}
                            </span>
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ strpos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ strpos($item->jenis->jenis_keuangan ?? '', 'PEMASUKAN') !== false ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                        <div class="space-y-2 text-xs sm:text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-medium">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis:</span>
                                <span class="font-medium">{{ $item->jenis->jenis_keuangan ?? '-' }}</span>
                            </div>
                            @if ($item->id_penjualan)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ref Penjualan:</span>
                                    <span
                                        class="font-medium">#{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            @endif
                            @if ($item->id_penerimaan)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ref Penerimaan:</span>
                                    <span
                                        class="font-medium">#{{ str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-gray-600">Nominal:</span>
                                <span
                                    class="font-bold
                                    {{ $item->jenis && strpos($item->jenis->jenis_keuangan, 'PEMASUKAN') !== false ? 'text-emerald-600' : 'text-red-600' }}">
                                    Rp {{ number_format($item->total_keuangan, 0, ',', '.') }}
                                </span>
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
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Referensi</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Nominal</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Tipe</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transaksi as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-mono font-semibold">
                                    #{{ str_pad($item->id_keuangan, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $item->jenis->jenis_keuangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($item->id_penjualan)
                                        <span class="text-blue-600">Penjualan
                                            #{{ str_pad($item->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
                                    @elseif($item->id_penerimaan)
                                        <span class="text-purple-600">Penerimaan
                                            #{{ str_pad($item->id_penerimaan, 6, '0', STR_PAD_LEFT) }}</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-right font-bold
                                    {{ $item->jenis && strpos($item->jenis->jenis_keuangan, 'PEMASUKAN') !== false ? 'text-emerald-600' : 'text-red-600' }}">
                                    Rp {{ number_format($item->total_keuangan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold
                                        {{ $item->jenis && strpos($item->jenis->jenis_keuangan, 'PEMASUKAN') !== false ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->jenis && strpos($item->jenis->jenis_keuangan, 'PEMASUKAN') !== false ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
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
            const labels = @json($keuanganPerHari->pluck('tanggal'));
            const dataPemasukan = @json($keuanganPerHari->pluck('pemasukan'));
            const dataPengeluaran = @json($keuanganPerHari->pluck('pengeluaran'));

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
            const ctx = document.getElementById('chartKeuangan').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: formatLabels,
                    datasets: [{
                        label: 'Pemasukan',
                        data: dataPemasukan,
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Pengeluaran',
                        data: dataPengeluaran,
                        backgroundColor: 'rgba(239, 68, 68, 0.2)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
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
                        }
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
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
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
                    chart.options.plugins.legend.position = isMobile ? 'bottom' : 'top';
                    chart.options.plugins.legend.labels.padding = isMobile ? 10 : 15;
                    chart.options.plugins.legend.labels.font.size = isMobile ? 11 : 13;

                    chart.update();
                }, 250);
            });
        </script>
    @endpush
@endsection
