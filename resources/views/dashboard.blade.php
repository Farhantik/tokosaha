@extends('layouts.app')

@section('title', 'Dashboard - Toko Sahabat')

@section('content')
    <div class="space-y-4 md:space-y-6 px-2 md:px-0">
        <h1 class="text-xl md:text-3xl font-bold text-gray-800">
            <i class="fas fa-home mr-2"></i>Dashboard
        </h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <!-- Transaksi Hari Ini -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-gray-600 text-xs md:text-sm truncate">Transaksi Hari Ini</p>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-800">{{ $totalTransaksiHariIni }}</h3>
                    </div>
                    <div class="bg-blue-100 p-2 md:p-3 rounded-full flex-shrink-0 ml-2">
                        <i class="fas fa-shopping-cart text-lg md:text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Omzet Hari Ini -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-gray-600 text-xs md:text-sm truncate">Omzet Hari Ini</p>
                        <h3 class="text-sm md:text-2xl font-bold text-gray-800 break-all">
                            Rp {{ number_format($totalOmzetHariIni, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0 ml-2">
                        <i class="fas fa-money-bill-wave text-lg md:text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Status Kasir -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-gray-600 text-xs md:text-sm">Status Kasir</p>
                        @if ($kasirAktif)
                            <h3 class="text-base md:text-lg font-bold text-green-600">
                                <i class="fas fa-check-circle"></i> Aktif
                            </h3>
                            <p class="text-xs text-gray-500 truncate">{{ $kasirAktif->user->nama_user }}</p>
                        @else
                            <h3 class="text-base md:text-lg font-bold text-red-600">
                                <i class="fas fa-times-circle"></i> Tutup
                            </h3>
                        @endif
                    </div>
                    <div class="bg-purple-100 p-2 md:p-3 rounded-full flex-shrink-0 ml-2">
                        <i class="fas fa-cash-register text-lg md:text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-gray-600 text-xs md:text-sm truncate">Stok Menipis</p>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-800">{{ $produkStokMenupis->count() }}</h3>
                    </div>
                    <div class="bg-orange-100 p-2 md:p-3 rounded-full flex-shrink-0 ml-2">
                        <i class="fas fa-exclamation-triangle text-lg md:text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            <!-- Grafik Penjualan -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <h2 class="text-base md:text-xl font-bold mb-4">
                    <i class="fas fa-chart-line mr-2"></i>Grafik Penjualan (7 Hari)
                </h2>
                <div style="position: relative; height: 220px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <h2 class="text-base md:text-xl font-bold mb-4">
                    <i class="fas fa-fire mr-2 text-orange-500"></i>Produk Terlaris
                    <span class="ml-2 text-xs md:text-sm font-normal text-gray-500">Top 5</span>
                </h2>
                <div class="space-y-2 md:space-y-3">
                    @php
                        $rankColors = [
                            1 => [
                                'bg' => 'bg-yellow-400',
                                'text' => 'text-yellow-900',
                                'icon' => 'fa-trophy',
                                'iconColor' => 'text-yellow-500',
                                'bar' => 'bg-yellow-400',
                            ],
                            2 => [
                                'bg' => 'bg-gray-300',
                                'text' => 'text-gray-800',
                                'icon' => 'fa-medal',
                                'iconColor' => 'text-gray-400',
                                'bar' => 'bg-gray-400',
                            ],
                            3 => [
                                'bg' => 'bg-orange-300',
                                'text' => 'text-orange-900',
                                'icon' => 'fa-medal',
                                'iconColor' => 'text-orange-400',
                                'bar' => 'bg-orange-400',
                            ],
                            4 => [
                                'bg' => 'bg-blue-100',
                                'text' => 'text-blue-800',
                                'icon' => 'fa-star',
                                'iconColor' => 'text-blue-400',
                                'bar' => 'bg-blue-400',
                            ],
                            5 => [
                                'bg' => 'bg-purple-100',
                                'text' => 'text-purple-800',
                                'icon' => 'fa-star',
                                'iconColor' => 'text-purple-400',
                                'bar' => 'bg-purple-400',
                            ],
                        ];
                        $maxTerjual = $produkTerlaris->take(5)->max('total_terjual') ?: 1;
                    @endphp

                    @forelse($produkTerlaris->take(5) as $rank => $produk)
                        @php
                            $rankNum = $rank + 1;
                            $rc = $rankColors[$rankNum] ?? [
                                'bg' => 'bg-gray-100',
                                'text' => 'text-gray-700',
                                'icon' => 'fa-star',
                                'iconColor' => 'text-gray-400',
                                'bar' => 'bg-gray-300',
                            ];
                            $pct = round(($produk->total_terjual / $maxTerjual) * 100);
                        @endphp
                        <div
                            class="flex items-center gap-2 md:gap-3 p-2 md:p-3 rounded-lg border border-gray-100 hover:shadow-sm transition-shadow {{ $rankNum <= 3 ? 'bg-gradient-to-r from-white to-gray-50' : 'bg-white' }}">
                            <div
                                class="flex-shrink-0 w-8 h-8 md:w-9 md:h-9 rounded-full {{ $rc['bg'] }} flex items-center justify-center font-bold text-xs md:text-sm {{ $rc['text'] }} shadow-sm">
                                {{ $rankNum }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1 mb-1">
                                    @if ($rankNum <= 3)
                                        <i class="fas {{ $rc['icon'] }} text-xs {{ $rc['iconColor'] }}"></i>
                                    @endif
                                    <p class="text-xs md:text-sm font-semibold text-gray-800 truncate">
                                        {{ $produk->nama_produk }}</p>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="{{ $rc['bar'] }} h-1.5 rounded-full transition-all duration-500"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-1 rounded-full text-xs font-bold {{ $rc['bg'] }} {{ $rc['text'] }}">
                                    {{ $produk->total_terjual }} terjual
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sisa Stok -->
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <div class="flex flex-col gap-3 mb-4 md:mb-6">
                <h2 class="text-base md:text-xl font-bold">
                    <i class="fas fa-box-open mr-2"></i>Sisa Stok
                    <span id="totalProduk"
                        class="ml-2 text-xs md:text-sm font-normal text-gray-500">({{ $produkStokMenupis->count() }}
                        produk)</span>
                </h2>

                <!-- Filter Kategori -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs md:text-sm font-semibold text-gray-700">
                        <i class="fas fa-filter mr-1 text-blue-500"></i>Filter:
                    </span>
                    <button onclick="filterKategori('all')"
                        class="filter-btn active px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700"
                        data-kategori="all">
                        <i class="fas fa-th mr-1"></i>Semua
                        <span
                            class="ml-1 bg-white text-blue-600 px-1.5 py-0.5 rounded-full text-xs">{{ $produkStokMenupis->count() }}</span>
                    </button>

                    @foreach ($kategoris as $kategori)
                        @php $count = $kategoriCounts[$kategori->id_produk_kategori] ?? 0; @endphp
                        @if ($count > 0)
                            <button onclick="filterKategori({{ $kategori->id_produk_kategori }})"
                                class="filter-btn px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                                data-kategori="{{ $kategori->id_produk_kategori }}">
                                {{ $kategori->nama_kategori }}
                                <span
                                    class="ml-1 bg-gray-300 text-gray-700 px-1.5 py-0.5 rounded-full text-xs">{{ $count }}</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>

            @if ($produkStokMenupis->count() > 0)
                <!-- Legend -->
                <div class="flex flex-wrap gap-2 md:gap-3 text-xs mb-4 pb-4 border-b">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-500 border border-red-600"></span>
                        <span class="text-gray-600">Habis (0)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-pink-400 border border-pink-500"></span>
                        <span class="text-gray-600">Kritis (1–5)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-orange-400 border border-orange-500"></span>
                        <span class="text-gray-600">Menipis (6–10)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-amber-400 border border-amber-500"></span>
                        <span class="text-gray-600">Perhatian (11–20)</span>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto -mx-4 md:mx-0">
                @if ($produkStokMenupis->count() > 0)
                    <table class="min-w-full" id="tabelStokMenipis">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-700 to-gray-800 text-white">
                                <th
                                    class="px-3 md:px-4 py-2 md:py-3 text-left text-xs font-semibold uppercase tracking-wider rounded-tl-lg">
                                    No</th>
                                <th
                                    class="hidden sm:table-cell px-3 md:px-4 py-2 md:py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                    Kode</th>
                                <th
                                    class="px-3 md:px-4 py-2 md:py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                    Nama Produk</th>
                                <th
                                    class="hidden md:table-cell px-3 md:px-4 py-2 md:py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-3 md:px-4 py-2 md:py-3 text-center text-xs font-semibold uppercase tracking-wider">
                                    Stok</th>
                                <th
                                    class="hidden sm:table-cell px-3 md:px-4 py-2 md:py-3 text-right text-xs font-semibold uppercase tracking-wider">
                                    Harga</th>
                                <th
                                    class="px-3 md:px-4 py-2 md:py-3 text-center text-xs font-semibold uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-3 md:px-4 py-2 md:py-3 text-center text-xs font-semibold uppercase tracking-wider rounded-tr-lg">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($produkStokMenupis as $index => $produk)
                                @php
                                    if ($produk->stock_produk <= 0) {
                                        $rowClass = 'border-l-4 border-red-600';
                                        $rowStyle = 'background: linear-gradient(90deg, #fecaca 0%, #fee2e2 100%);';
                                        $badgeClass = 'bg-red-600 text-white';
                                        $stockNumClass = 'bg-red-600 text-white';
                                        $statusText = 'HABIS';
                                        $statusIcon = 'fa-times-circle';
                                    } elseif ($produk->stock_produk <= 5) {
                                        $rowClass = 'border-l-4 border-pink-500';
                                        $rowStyle = 'background: linear-gradient(90deg, #fce7f3 0%, #fdf2f8 100%);';
                                        $badgeClass = 'bg-pink-500 text-white';
                                        $stockNumClass = 'bg-pink-500 text-white';
                                        $statusText = 'KRITIS';
                                        $statusIcon = 'fa-exclamation-triangle';
                                    } elseif ($produk->stock_produk <= 10) {
                                        $rowClass = 'border-l-4 border-orange-500';
                                        $rowStyle = 'background: linear-gradient(90deg, #fed7aa 0%, #fff7ed 100%);';
                                        $badgeClass = 'bg-orange-500 text-white';
                                        $stockNumClass = 'bg-orange-500 text-white';
                                        $statusText = 'MENIPIS';
                                        $statusIcon = 'fa-exclamation-circle';
                                    } else {
                                        $rowClass = 'border-l-4 border-amber-400';
                                        $rowStyle = 'background: linear-gradient(90deg, #fef3c7 0%, #fffbeb 100%);';
                                        $badgeClass = 'bg-amber-400 text-amber-900';
                                        $stockNumClass = 'bg-amber-400 text-amber-900';
                                        $statusText = 'PERHATIAN';
                                        $statusIcon = 'fa-info-circle';
                                    }
                                @endphp
                                <tr class="produk-row {{ $rowClass }} transition-all duration-150 hover:brightness-95"
                                    style="{{ $rowStyle }}"
                                    data-kategori="{{ $produk->id_produk_kategori ?? 'null' }}">
                                    <td
                                        class="px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-xs md:text-sm font-bold text-gray-700">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="hidden sm:table-cell px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-700 font-mono">
                                        {{ $produk->kode_produk ?? '-' }}
                                    </td>
                                    <td
                                        class="px-3 md:px-4 py-2 md:py-3 text-xs md:text-sm font-semibold text-gray-900 max-w-[130px] md:max-w-none">
                                        <span class="block truncate">{{ $produk->nama_produk }}</span>
                                        {{-- Di mobile: tampilkan kategori di bawah nama produk --}}
                                        <span
                                            class="md:hidden block text-xs font-normal text-gray-500 mt-0.5">{{ $produk->nama_kategori ?? '-' }}</span>
                                    </td>
                                    <td
                                        class="hidden md:table-cell px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-600">
                                        <span
                                            class="inline-flex px-2 py-1 bg-white rounded text-xs border border-gray-300 shadow-sm">
                                            {{ $produk->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-10 md:w-16 px-1 md:px-3 py-1 rounded-full text-xs md:text-sm font-bold shadow-sm {{ $stockNumClass }}">
                                            {{ $produk->stock_produk }}
                                        </span>
                                    </td>
                                    <td
                                        class="hidden sm:table-cell px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-xs md:text-sm text-right font-semibold text-gray-900">
                                        Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-center">
                                        {{-- Di mobile hanya icon, di desktop tampilkan teks --}}
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 md:px-2 py-1 rounded-full text-xs font-bold shadow-sm {{ $badgeClass }}">
                                            <i class="fas {{ $statusIcon }}"></i>
                                            <span class="hidden sm:inline">{{ $statusText }}</span>
                                        </span>
                                    </td>
                                    <td class="px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-center">
                                        <button
                                            onclick="showDetailModal({{ json_encode($produk) }}, '{{ $statusText }}', '{{ $badgeClass }}')"
                                            class="inline-flex items-center px-2 md:px-3 py-1 md:py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors duration-150 shadow-sm">
                                            <i class="fas fa-eye md:mr-1"></i>
                                            <span class="hidden md:inline">Detail</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Info Box -->
                    <div class="mt-4 p-3 md:p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r">
                        <div class="flex items-start">
                            <i
                                class="fas fa-info-circle text-blue-600 mt-0.5 mr-2 md:mr-3 text-base md:text-lg flex-shrink-0"></i>
                            <div class="text-xs md:text-sm text-blue-800">
                                <p class="font-semibold mb-1">Informasi Stok:</p>
                                <p>Menampilkan <span id="infoJumlah"
                                        class="font-bold">{{ $produkStokMenupis->count() }}</span> produk yang memerlukan
                                    perhatian untuk restok segera.</p>
                                <p class="text-xs mt-1 text-blue-700">
                                    <i class="fas fa-lightbulb mr-1"></i>Gunakan filter kategori untuk mempermudah
                                    pencarian produk.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 md:py-16">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-full bg-green-100 mb-4">
                            <i class="fas fa-check-circle text-3xl md:text-4xl text-green-600"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Semua Stok Aman!</h3>
                        <p class="text-sm text-gray-600 mb-1">Tidak ada produk dengan stok menipis saat ini.</p>
                        <p class="text-xs text-gray-500">Sistem akan menampilkan produk dengan stok ≤ 20 unit</p>
                        <div
                            class="mt-4 md:mt-6 inline-flex items-center gap-2 px-3 md:px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-xs md:text-sm text-blue-800">
                            <i class="fas fa-lightbulb flex-shrink-0"></i>
                            <span>Tip: Lakukan pengecekan stok secara berkala untuk antisipasi kebutuhan restok</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Detail Produk -->
    <div id="detailModal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 p-3 md:p-4">
        <div
            class="relative top-4 md:top-20 mx-auto p-4 md:p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 md:pb-4 border-b">
                <h3 class="text-base md:text-xl font-bold text-gray-800">
                    <i class="fas fa-box-open mr-2"></i>Detail Produk
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i class="fas fa-times text-xl md:text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-3 md:mt-4">
                <div class="flex justify-center mb-4 md:mb-6">
                    <div class="relative">
                        <img id="modalGambar" src="" alt="Gambar Produk"
                            class="w-32 h-32 md:w-48 md:h-48 object-cover rounded-lg shadow-md border-2 border-gray-200"
                            onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        <div id="modalStatusBadge" class="absolute -top-2 -right-2"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                    <div class="bg-gray-50 p-3 md:p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Kode Produk</p>
                        <p id="modalKode" class="text-sm md:text-base font-mono font-bold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 p-3 md:p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Nama Produk</p>
                        <p id="modalNama" class="text-sm md:text-base font-bold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 p-3 md:p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Kategori</p>
                        <p id="modalKategori" class="text-sm md:text-base font-semibold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 p-3 md:p-4 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Stok Tersedia</p>
                        <p id="modalStok" class="text-xl md:text-2xl font-bold text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 p-3 md:p-4 rounded-lg border border-gray-200 sm:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Harga Jual</p>
                        <p id="modalHarga" class="text-xl md:text-2xl font-bold text-green-600"></p>
                    </div>
                </div>

                <div id="modalAlert" class="mt-4 md:mt-6 p-3 md:p-4 rounded-lg border-l-4">
                    <div class="flex items-start">
                        <i id="modalAlertIcon" class="mt-0.5 mr-2 md:mr-3 text-base md:text-lg flex-shrink-0"></i>
                        <div class="text-xs md:text-sm">
                            <p id="modalAlertText" class="font-semibold mb-1"></p>
                            <p id="modalAlertDesc"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4 md:mt-6 pt-3 md:pt-4 border-t">
                <button onclick="closeDetailModal()"
                    class="px-4 md:px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors duration-150">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('salesChart');
                if (ctx) {
                    try {
                        new Chart(ctx, {
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
                                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: val => 'Rp ' + val.toLocaleString('id-ID'),
                                            maxTicksLimit: 5
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 0
                                        }
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        console.error('Chart error:', e);
                    }
                }
            });

            function filterKategori(kategoriId) {
                const rows = document.querySelectorAll('.produk-row');
                const filterBtns = document.querySelectorAll('.filter-btn');
                let visibleCount = 0;

                filterBtns.forEach(btn => {
                    if (btn.dataset.kategori == kategoriId) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'active');
                    } else {
                        btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'active');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                rows.forEach(row => {
                    const rowKategori = row.dataset.kategori;
                    if (kategoriId === 'all' || rowKategori == kategoriId) {
                        row.style.display = '';
                        visibleCount++;
                        row.querySelector('td:first-child').textContent = visibleCount;
                    } else {
                        row.style.display = 'none';
                    }
                });

                document.getElementById('totalProduk').textContent = `(${visibleCount} produk)`;
                document.getElementById('infoJumlah').textContent = visibleCount;

                const tbody = document.querySelector('#tabelStokMenipis tbody');
                const existingEmptyRow = tbody.querySelector('.empty-row');

                if (visibleCount === 0 && !existingEmptyRow) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-row';
                    emptyRow.innerHTML = `
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-400 mb-2 block"></i>
                            <p class="font-semibold">Tidak ada produk di kategori ini</p>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                } else if (visibleCount > 0 && existingEmptyRow) {
                    existingEmptyRow.remove();
                }
            }

            function showDetailModal(produk, statusText, badgeClass) {
                const modal = document.getElementById('detailModal');

                const gambarUrl = produk.gambar_produk ?
                    `/uploads/produk/${produk.gambar_produk}` :
                    'https://via.placeholder.com/200x200?text=No+Image';
                document.getElementById('modalGambar').src = gambarUrl;

                const iconMap = {
                    'HABIS': 'fa-times-circle',
                    'KRITIS': 'fa-exclamation-triangle',
                    'MENIPIS': 'fa-exclamation-circle',
                    'PERHATIAN': 'fa-info-circle'
                };
                document.getElementById('modalStatusBadge').innerHTML = `
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold shadow-lg ${badgeClass}">
                        <i class="fas ${iconMap[statusText] ?? 'fa-info-circle'}"></i>
                        ${statusText}
                    </span>
                `;

                document.getElementById('modalKode').textContent = produk.kode_produk || '-';
                document.getElementById('modalNama').textContent = produk.nama_produk;
                document.getElementById('modalKategori').textContent = produk.nama_kategori || '-';
                document.getElementById('modalStok').textContent = produk.stock_produk + ' Unit';
                document.getElementById('modalHarga').textContent = 'Rp ' + parseInt(produk.harga_produk).toLocaleString(
                    'id-ID');

                const alertConfig = {
                    'HABIS': {
                        box: 'bg-red-50 border-red-500',
                        icon: 'fas fa-times-circle text-red-600',
                        textCls: 'font-semibold mb-1 text-red-800',
                        descCls: 'text-xs text-red-700',
                        title: '⚠️ Stok Habis!',
                        desc: 'Produk ini sudah tidak tersedia. Segera lakukan restok.'
                    },
                    'KRITIS': {
                        box: 'bg-pink-50 border-pink-400',
                        icon: 'fas fa-exclamation-triangle text-pink-500',
                        textCls: 'font-semibold mb-1 text-pink-800',
                        descCls: 'text-xs text-pink-700',
                        title: '🚨 Stok Kritis!',
                        desc: 'Stok hampir habis. Prioritaskan restok produk ini.'
                    },
                    'MENIPIS': {
                        box: 'bg-orange-50 border-orange-400',
                        icon: 'fas fa-exclamation-circle text-orange-500',
                        textCls: 'font-semibold mb-1 text-orange-800',
                        descCls: 'text-xs text-orange-700',
                        title: '⚡ Stok Menipis!',
                        desc: 'Stok mulai berkurang. Rencanakan restok segera.'
                    },
                    'PERHATIAN': {
                        box: 'bg-amber-50 border-amber-400',
                        icon: 'fas fa-info-circle text-amber-500',
                        textCls: 'font-semibold mb-1 text-amber-800',
                        descCls: 'text-xs text-amber-700',
                        title: '💡 Perlu Perhatian',
                        desc: 'Pantau stok produk ini secara berkala.'
                    },
                };

                const cfg = alertConfig[statusText] ?? alertConfig['PERHATIAN'];
                document.getElementById('modalAlert').className = `mt-4 md:mt-6 p-3 md:p-4 rounded-lg border-l-4 ${cfg.box}`;
                document.getElementById('modalAlertIcon').className =
                    `${cfg.icon} mt-0.5 mr-2 md:mr-3 text-base md:text-lg flex-shrink-0`;
                document.getElementById('modalAlertText').className = cfg.textCls;
                document.getElementById('modalAlertText').textContent = cfg.title;
                document.getElementById('modalAlertDesc').className = cfg.descCls;
                document.getElementById('modalAlertDesc').textContent = cfg.desc;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeDetailModal() {
                document.getElementById('detailModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            document.getElementById('detailModal').addEventListener('click', function(e) {
                if (e.target === this) closeDetailModal();
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeDetailModal();
            });
        </script>
    @endpush
@endsection
