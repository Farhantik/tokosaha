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
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalOmzetHariIni, 0, ',', '.') }}</h3>
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

        <!-- Stok Menipis - DENGAN FILTER -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-box-open mr-2"></i>Produk Stok Menipis
                    <span id="totalProduk" class="ml-2 text-sm font-normal text-gray-500">({{ $produkStokMenupis->count() }} produk)</span>
                </h2>
                
                <!-- Filter Kategori -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-gray-700">Filter:</span>
                    <button onclick="filterKategori('all')" 
                            class="filter-btn active px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700"
                            data-kategori="all">
                        <i class="fas fa-th mr-1"></i>
                        Semua <span class="ml-1 bg-white text-blue-600 px-2 py-0.5 rounded-full text-xs">{{ $produkStokMenupis->count() }}</span>
                    </button>
                    @foreach($kategoris as $kategori)
                        @php
                            $count = $kategoriCounts[$kategori->id_produk_kategori] ?? 0;
                        @endphp
                        @if($count > 0)
                            <button onclick="filterKategori({{ $kategori->id_produk_kategori }})" 
                                    class="filter-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                                    data-kategori="{{ $kategori->id_produk_kategori }}">
                                {{ $kategori->nama_kategori }} 
                                <span class="ml-1 bg-gray-300 text-gray-700 px-2 py-0.5 rounded-full text-xs">{{ $count }}</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>

            @if ($produkStokMenupis->count() > 0)
                <!-- Legend Warna -->
                <div class="flex flex-wrap gap-3 text-xs mb-4 pb-4 border-b">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-100 border border-red-300"></span>
                        <span class="text-gray-600">Habis (0)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-50 border border-red-200"></span>
                        <span class="text-gray-600">Kritis (1-5)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-orange-50 border border-orange-200"></span>
                        <span class="text-gray-600">Menipis (6-10)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-yellow-50 border border-yellow-200"></span>
                        <span class="text-gray-600">Perhatian (11-20)</span>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                @if ($produkStokMenupis->count() > 0)
                    <!-- Tabel dengan Data -->
                    <table class="min-w-full" id="tabelStokMenipis">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($produkStokMenupis as $index => $produk)
                                @php
                                    // Tentukan warna berdasarkan stok
                                    if ($produk->stock_produk <= 0) {
                                        $rowClass = 'bg-red-100 hover:bg-red-150 border-l-4 border-red-500';
                                        $badgeClass = 'bg-red-600 text-white';
                                        $statusText = 'HABIS';
                                        $statusIcon = 'fa-times-circle';
                                    } elseif ($produk->stock_produk >= 1 && $produk->stock_produk <= 5) {
                                        $rowClass = 'bg-red-50 hover:bg-red-100 border-l-4 border-red-400';
                                        $badgeClass = 'bg-red-500 text-white';
                                        $statusText = 'KRITIS';
                                        $statusIcon = 'fa-exclamation-triangle';
                                    } elseif ($produk->stock_produk >= 6 && $produk->stock_produk <= 10) {
                                        $rowClass = 'bg-orange-50 hover:bg-orange-100 border-l-4 border-orange-400';
                                        $badgeClass = 'bg-orange-500 text-white';
                                        $statusText = 'MENIPIS';
                                        $statusIcon = 'fa-exclamation-circle';
                                    } else {
                                        $rowClass = 'bg-yellow-50 hover:bg-yellow-100 border-l-4 border-yellow-400';
                                        $badgeClass = 'bg-yellow-500 text-white';
                                        $statusText = 'PERHATIAN';
                                        $statusIcon = 'fa-info-circle';
                                    }
                                @endphp
                                
                                <tr class="produk-row {{ $rowClass }} transition-colors duration-150" 
                                    data-kategori="{{ $produk->id_produk_kategori ?? 'null' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        {{ $produk->kode_produk ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                        {{ $produk->nama_produk }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        <span class="inline-flex px-2 py-1 bg-white rounded text-xs border border-gray-300">
                                            {{ $produk->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center w-16 px-3 py-1 rounded-full text-sm font-bold {{ $badgeClass }}">
                                            {{ $produk->stock_produk }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                        Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                                            <i class="fas {{ $statusIcon }}"></i>
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <button 
                                            onclick="showDetailModal({{ json_encode($produk) }}, '{{ $statusText }}', '{{ $badgeClass }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors duration-150 shadow-sm">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Info Box -->
                    <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3 text-lg"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Informasi Stok:</p>
                                <p>Menampilkan <span id="infoJumlah" class="font-bold">{{ $produkStokMenupis->count() }}</span> produk yang memerlukan perhatian untuk restok segera.</p>
                                <p class="text-xs mt-1 text-blue-700">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    Gunakan filter kategori untuk mempermudah pencarian produk
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Tampilan Kosong -->
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
                            <i class="fas fa-check-circle text-4xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Semua Stok Aman!</h3>
                        <p class="text-gray-600 mb-1">Tidak ada produk dengan stok menipis saat ini.</p>
                        <p class="text-sm text-gray-500">Sistem akan menampilkan produk dengan stok ≤ 20 unit</p>
                        
                        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                            <i class="fas fa-lightbulb"></i>
                            <span>Tip: Lakukan pengecekan stok secara berkala untuk antisipasi kebutuhan restok</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Detail Produk -->
    <div id="detailModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-box-open mr-2"></i>Detail Produk
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="mt-4">
                <!-- Gambar Produk -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <img id="modalGambar" 
                             src="" 
                             alt="Gambar Produk" 
                             class="w-48 h-48 object-cover rounded-lg shadow-md border-2 border-gray-200"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        <div id="modalStatusBadge" class="absolute -top-2 -right-2">
                            <!-- Status badge will be inserted here -->
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kode Produk -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Kode Produk</p>
                        <p id="modalKode" class="text-base font-mono font-bold text-gray-800"></p>
                    </div>

                    <!-- Nama Produk -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Nama Produk</p>
                        <p id="modalNama" class="text-base font-bold text-gray-800"></p>
                    </div>

                    <!-- Kategori -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Kategori</p>
                        <p id="modalKategori" class="text-base font-semibold text-gray-800"></p>
                    </div>

                    <!-- Stok -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Stok Tersedia</p>
                        <p id="modalStok" class="text-2xl font-bold text-gray-800"></p>
                    </div>

                    <!-- Harga -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Harga Jual</p>
                        <p id="modalHarga" class="text-2xl font-bold text-green-600"></p>
                    </div>
                </div>

                <!-- Alert Box -->
                <div id="modalAlert" class="mt-6 p-4 rounded-lg border-l-4">
                    <div class="flex items-start">
                        <i id="modalAlertIcon" class="mt-0.5 mr-3 text-lg"></i>
                        <div class="text-sm">
                            <p id="modalAlertText" class="font-semibold mb-1"></p>
                            <p id="modalAlertDesc" class="text-xs"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button onclick="closeDetailModal()" 
                        class="px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-150">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
                                    fill: true,
                                    pointBackgroundColor: 'rgb(37, 99, 235)',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: 'rgb(37, 99, 235)'
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
                        console.log('✅ Chart berhasil dibuat');
                    } catch (error) {
                        console.error('❌ Error creating chart:', error);
                    }
                }
            });

            // Function Filter Kategori
            function filterKategori(kategoriId) {
                const rows = document.querySelectorAll('.produk-row');
                const filterBtns = document.querySelectorAll('.filter-btn');
                let visibleCount = 0;

                // Update button active state
                filterBtns.forEach(btn => {
                    if (btn.dataset.kategori == kategoriId) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'active');
                    } else {
                        btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'active');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                // Filter rows
                rows.forEach((row, index) => {
                    const rowKategori = row.dataset.kategori;
                    
                    if (kategoriId === 'all' || rowKategori == kategoriId) {
                        row.style.display = '';
                        visibleCount++;
                        // Update nomor urut
                        row.querySelector('td:first-child').textContent = visibleCount;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update counter
                document.getElementById('totalProduk').textContent = `(${visibleCount} produk)`;
                document.getElementById('infoJumlah').textContent = visibleCount;

                // Show empty state if no results
                const tbody = document.querySelector('#tabelStokMenipis tbody');
                const existingEmptyRow = tbody.querySelector('.empty-row');
                
                if (visibleCount === 0 && !existingEmptyRow) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-row';
                    emptyRow.innerHTML = `
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                            <p class="font-semibold">Tidak ada produk di kategori ini</p>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                } else if (visibleCount > 0 && existingEmptyRow) {
                    existingEmptyRow.remove();
                }
            }

            // Function untuk menampilkan modal detail
            function showDetailModal(produk, statusText, badgeClass) {
                const modal = document.getElementById('detailModal');
                
                // Set gambar - PERBAIKAN: Gunakan path yang sama dengan transaksi
                let gambarUrl;
                if (produk.gambar_produk) {
                    gambarUrl = `/uploads/produk/${produk.gambar_produk}`;
                } else {
                    gambarUrl = 'https://via.placeholder.com/200x200?text=No+Image';
                }
                
                document.getElementById('modalGambar').src = gambarUrl;
                
                // Set status badge
                const statusBadge = document.getElementById('modalStatusBadge');
                const iconMap = {
                    'HABIS': 'fa-times-circle',
                    'KRITIS': 'fa-exclamation-triangle',
                    'MENIPIS': 'fa-exclamation-circle',
                    'PERHATIAN': 'fa-info-circle'
                };
                statusBadge.innerHTML = `
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold shadow-lg ${badgeClass}">
                        <i class="fas ${iconMap[statusText]}"></i>
                        ${statusText}
                    </span>
                `;
                
                // Set informasi produk
                document.getElementById('modalKode').textContent = produk.kode_produk || '-';
                document.getElementById('modalNama').textContent = produk.nama_produk;
                document.getElementById('modalKategori').textContent = produk.nama_kategori || '-';
                document.getElementById('modalStok').textContent = produk.stock_produk + ' Unit';
                document.getElementById('modalHarga').textContent = 'Rp ' + parseInt(produk.harga_produk).toLocaleString('id-ID');
                
                // Set alert box berdasarkan status
                const alertBox = document.getElementById('modalAlert');
                const alertIcon = document.getElementById('modalAlertIcon');
                const alertText = document.getElementById('modalAlertText');
                const alertDesc = document.getElementById('modalAlertDesc');
                
                if (statusText === 'HABIS') {
                    alertBox.className = 'mt-6 p-4 rounded-lg border-l-4 bg-red-50 border-red-500';
                    alertIcon.className = 'fas fa-times-circle text-red-600 mt-0.5 mr-3 text-lg';
                    alertText.className = 'font-semibold mb-1 text-red-800';
                    alertText.textContent = '⚠️ Stok Habis!';
                    alertDesc.className = 'text-xs text-red-700';
                    alertDesc.textContent = 'Produk ini sudah tidak tersedia. Segera lakukan restok untuk menghindari kehilangan penjualan.';
                } else if (statusText === 'KRITIS') {
                    alertBox.className = 'mt-6 p-4 rounded-lg border-l-4 bg-red-50 border-red-400';
                    alertIcon.className = 'fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3 text-lg';
                    alertText.className = 'font-semibold mb-1 text-red-800';
                    alertText.textContent = '🚨 Stok Kritis!';
                    alertDesc.className = 'text-xs text-red-700';
                    alertDesc.textContent = 'Stok hampir habis. Prioritaskan restok produk ini dalam waktu dekat.';
                } else if (statusText === 'MENIPIS') {
                    alertBox.className = 'mt-6 p-4 rounded-lg border-l-4 bg-orange-50 border-orange-400';
                    alertIcon.className = 'fas fa-exclamation-circle text-orange-500 mt-0.5 mr-3 text-lg';
                    alertText.className = 'font-semibold mb-1 text-orange-800';
                    alertText.textContent = '⚡ Stok Menipis!';
                    alertDesc.className = 'text-xs text-orange-700';
                    alertDesc.textContent = 'Stok mulai berkurang. Rencanakan restok untuk menghindari kehabisan.';
                } else {
                    alertBox.className = 'mt-6 p-4 rounded-lg border-l-4 bg-yellow-50 border-yellow-400';
                    alertIcon.className = 'fas fa-info-circle text-yellow-600 mt-0.5 mr-3 text-lg';
                    alertText.className = 'font-semibold mb-1 text-yellow-800';
                    alertText.textContent = '💡 Perlu Perhatian';
                    alertDesc.className = 'text-xs text-yellow-700';
                    alertDesc.textContent = 'Pantau stok produk ini secara berkala untuk antisipasi kebutuhan restok.';
                }
                
                // Tampilkan modal
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            // Function untuk menutup modal
            function closeDetailModal() {
                const modal = document.getElementById('detailModal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal ketika klik di luar modal
            document.getElementById('detailModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDetailModal();
                }
            });

            // Close modal dengan ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDetailModal();
                }
            });
        </script>
    @endpush
@endsection