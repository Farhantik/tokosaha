@extends('layouts.app')

@section('title', 'Log Aktivitas Produk')
@section('page-title', 'Log Aktivitas Produk')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header dengan tombol kembali -->
        <div class="flex items-center gap-4">
            <a href="{{ route('produk.index') }}"
                class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div class="flex-1">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Log Aktivitas Produk</h2>
                <p class="text-xs sm:text-sm text-gray-500">Tracking perubahan dan transaksi</p>
            </div>
        </div>

        <!-- Info Produk Card -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center gap-4">
                @if ($produk->gambar_produk)
                    <img src="{{ asset('uploads/produk/' . $produk->gambar_produk) }}" alt="{{ $produk->nama_produk }}"
                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-xl border-2 border-white/30">
                @else
                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-xl flex items-center justify-center border-2 border-white/30">
                        <i class="fas fa-image text-2xl sm:text-3xl text-white/60"></i>
                    </div>
                @endif

                <div class="flex-1">
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">{{ $produk->nama_produk }}</h3>
                    <p class="text-sm text-white/80">{{ $produk->code_produk ?? '-' }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                            <i class="fas fa-tag mr-1.5"></i>{{ $produk->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                            <i class="fas fa-box mr-1.5"></i>Stok: {{ $produk->stock_produk }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                            Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Log -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-2xl text-blue-600"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-xs sm:text-sm font-medium mb-1">Total Log</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalLogs }}</p>
            </div>

            <!-- Penjualan -->
            <!--<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">-->
            <!--    <div class="flex items-center justify-between mb-3">-->
            <!--        <div-->
            <!--            class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">-->
            <!--            <i class="fas fa-shopping-cart text-2xl text-purple-600"></i>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <p class="text-gray-500 text-xs sm:text-sm font-medium mb-1">Penjualan</p>-->
            <!--    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalPenjualan }}</p>-->
            <!--</div>-->

            <!-- Tambah Stok -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-up text-2xl text-green-600"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-xs sm:text-sm font-medium mb-1">Tambah Stok</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalPenerimaan }}</p>
            </div>

            <!-- Penjualan Stok -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-down text-2xl text-orange-600"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-xs sm:text-sm font-medium mb-1">Penjualan Stok</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalStokKeluar }}</p>
            </div>
        </div>

        <!-- Timeline Aktivitas -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-blue-600 mr-2"></i>Timeline Aktivitas
            </h3>

            @forelse($logs as $log)
                <div class="relative pl-8 pb-8 last:pb-0">
                    <!-- Timeline Line -->
                    @if (!$loop->last)
                        <div class="absolute left-3.5 top-8 bottom-0 w-0.5 bg-gray-200"></div>
                    @endif

                    <!-- Timeline Dot -->
                    <div
                        class="absolute left-0 top-0 w-7 h-7 rounded-full flex items-center justify-center
                        {{ $log->badge_color === 'green' ? 'bg-green-100' : '' }}
                        {{ $log->badge_color === 'blue' ? 'bg-blue-100' : '' }}
                        {{ $log->badge_color === 'orange' ? 'bg-orange-100' : '' }}
                        {{ $log->badge_color === 'yellow' ? 'bg-yellow-100' : '' }}
                        {{ $log->badge_color === 'gray' ? 'bg-gray-100' : '' }}">
                        <i
                            class="fas {{ $log->icon }} text-xs
                            {{ $log->badge_color === 'green' ? 'text-green-600' : '' }}
                            {{ $log->badge_color === 'blue' ? 'text-blue-600' : '' }}
                            {{ $log->badge_color === 'orange' ? 'text-orange-600' : '' }}
                            {{ $log->badge_color === 'yellow' ? 'text-yellow-600' : '' }}
                            {{ $log->badge_color === 'gray' ? 'text-gray-600' : '' }}"></i>
                    </div>

                    <!-- Content -->
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-lg
                                        {{ $log->badge_color === 'green' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}
                                        {{ $log->badge_color === 'blue' ? 'bg-blue-100 text-blue-700 border border-blue-200' : '' }}
                                        {{ $log->badge_color === 'orange' ? 'bg-orange-100 text-orange-700 border border-orange-200' : '' }}
                                        {{ $log->badge_color === 'yellow' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : '' }}
                                        {{ $log->badge_color === 'gray' ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->jenis_aktivitas)) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 font-medium mb-2">{{ $log->keterangan }}</p>

                                <!-- Detail Info -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                    @if ($log->stok_sebelum !== null)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-500">Stok Awal:</span>
                                            <span class="font-semibold">{{ $log->stok_sebelum }}</span>
                                        </div>
                                    @endif
                                    @if ($log->stok_sesudah !== null)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-500">Stok Akhir:</span>
                                            <span class="font-semibold">{{ $log->stok_sesudah }}</span>
                                        </div>
                                    @endif
                                    @if ($log->jumlah_perubahan && $log->jumlah_perubahan != 0)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-500">Perubahan:</span>
                                            <span class="font-semibold text-gray-700">
                                                {{ abs($log->jumlah_perubahan) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if ($log->harga_saat_itu)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-500">Harga:</span>
                                            <span class="font-semibold">Rp
                                                {{ number_format($log->harga_saat_itu, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                @if ($log->user_nama)
                                    <p class="text-xs text-gray-600 font-medium mt-1">
                                        <i class="fas fa-user text-gray-400 mr-1"></i>{{ $log->user_nama }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if ($log->id_penjualan)
                            <div class="inline-flex items-center text-xs text-blue-600 font-medium mt-2">
                                <i class="fas fa-receipt mr-1"></i>ID Transaksi: #{{ $log->id_penjualan }}
                            </div>
                        @endif

                        @if ($log->id_penerimaan)
                            <div class="inline-flex items-center text-xs text-green-600 font-medium mt-2">
                                <i class="fas fa-box mr-1"></i>ID Penerimaan: #{{ $log->id_penerimaan }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada aktivitas</p>
                    <p class="text-gray-400 text-sm mt-1">Log akan muncul saat ada perubahan pada produk</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection