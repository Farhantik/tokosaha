@extends('layouts.app')

@section('title', 'Dashboard - Toko Sahabat')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
            <i class="fas fa-home mr-2"></i>Dashboard
        </h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Transaksi Hari Ini -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Transaksi Hari Ini</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $totalTransaksiHariIni }}</h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Omzet Hari Ini -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Omzet Hari Ini</p>
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalOmzetHariIni, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Status Kasir -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Status Kasir</p>
                        @if ($kasirAktif)
                            <h3 class="text-lg font-bold text-green-600">
                                <i class="fas fa-check-circle"></i> Aktif
                            </h3>
                            <p class="text-xs text-gray-500">{{ $kasirAktif->user->nama_user }}</p>
                        @else
                            <h3 class="text-lg font-bold text-red-600">
                                <i class="fas fa-times-circle"></i> Tutup
                            </h3>
                        @endif
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-cash-register text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Produk Stok Menipis</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $produkStokMenupis->count() }}</h3>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Grafik Penjualan -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-chart-line mr-2"></i>Grafik Penjualan (7 Hari)
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-fire mr-2"></i>Produk Terlaris
                </h2>
                <div class="space-y-3">
                    @forelse($produkTerlaris as $produk)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <span class="font-semibold">{{ $produk->nama_produk }}</span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                {{ $produk->total_terjual }} terjual
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        @if ($produkStokMenupis->count() > 0)
            <div class="bg-white rounded-lg shadow p-6" id="stokMenupisSection">
                <h2 class="text-xl font-bold mb-4">
                    <i class="fas fa-box-open mr-2"></i>Produk Stok Menipis
                </h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed" id="stokMenupisTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-2/5 px-4 py-2 text-left text-sm font-semibold">Produk</th>
                                <th class="w-2/5 px-4 py-2 text-left text-sm font-semibold">Kategori</th>
                                <th class="w-1/5 px-4 py-2 text-center text-sm font-semibold">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($produkStokMenupis as $index => $produk)
                                <tr class="hover:bg-gray-50" data-row="{{ $index }}">
                                    <td class="px-4 py-3 text-sm" data-col="produk">
                                        {{ $produk->nama_produk }}
                                    </td>
                                    <td class="px-4 py-3 text-sm" data-col="kategori">
                                        {{ $produk->nama_kategori ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center" data-col="stok">
                                        <span
                                            style="display: inline-block !important; background-color: #fee2e2 !important; color: #991b1b !important; padding: 0.25rem 0.75rem !important; border-radius: 9999px !important; font-size: 0.875rem !important; font-weight: 600 !important; visibility: visible !important; opacity: 1 !important;">
                                            {{ $produk->stock_produk }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada produk dengan stok menipis
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Wait for DOM to be fully loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Debug: Check if table exists
                const table = document.getElementById('stokMenupisTable');
                if (table) {
                    console.log('✅ Tabel stok menipis ditemukan');
                    const rows = table.querySelectorAll('tbody tr');
                    console.log('✅ Jumlah baris:', rows.length);

                    // Check each row
                    rows.forEach((row, index) => {
                        const cells = row.querySelectorAll('td');
                        console.log(`Baris ${index}:`, {
                            produk: cells[0]?.textContent,
                            kategori: cells[1]?.textContent,
                            stok: cells[2]?.textContent,
                            jumlahKolom: cells.length
                        });
                    });

                    // Monitor DOM changes
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                                console.warn('⚠️ DOM berubah:', mutation);
                            }
                        });
                    });

                    observer.observe(table, {
                        childList: true,
                        subtree: true,
                        attributes: true
                    });
                } else {
                    console.error('❌ Tabel stok menipis TIDAK ditemukan');
                }

                // Initialize Chart
                const ctx = document.getElementById('salesChart');
                if (ctx) {
                    try {
                        const salesChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($grafikPenjualan->pluck('tanggal')),
                                datasets: [{
                                    label: 'Omzet (Rp)',
                                    data: @json($grafikPenjualan->pluck('total')),
                                    borderColor: 'rgb(37, 99, 235)',
                                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Chart berhasil dibuat');
                    } catch (error) {
                        console.error('Error creating chart:', error);
                    }
                }
            });
        </script>
    @endpush
@endsection
