@extends('layouts.app')

@section('title', 'Detail Pelanggan - Toko Sahabat')
@section('page-title', 'Detail Pelanggan')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header with Back Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center w-full sm:w-auto">
                <a href="{{ route('pelanggan.index') }}"
                    class="mr-3 w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all">
                    <i class="fas fa-arrow-left text-white text-lg sm:text-xl"></i>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Pelanggan</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Informasi lengkap pelanggan</p>
                </div>
            </div>
        </div>

        <!-- Customer Info Card -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-3xl sm:text-4xl font-bold border-4 border-white">
                    {{ strtoupper(substr($pelanggan->nama_pelanggan, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-xl sm:text-2xl font-bold mb-2">{{ $pelanggan->nama_pelanggan }}</h3>
                    <div class="space-y-1 text-sm sm:text-base opacity-90">
                        @if($pelanggan->no_telp)
                            <p><i class="fas fa-phone mr-2"></i>{{ $pelanggan->no_telp }}</p>
                        @endif
                        @if($pelanggan->email)
                            <p><i class="fas fa-envelope mr-2"></i>{{ $pelanggan->email }}</p>
                        @endif
                        @if($pelanggan->alamat)
                            <p><i class="fas fa-map-marker-alt mr-2"></i>{{ $pelanggan->alamat }}</p>
                        @endif
                    </div>
                    <div class="mt-3">
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-lg text-sm font-semibold">
                            Status: {{ ucfirst($pelanggan->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Total Piutang</p>
                    <p class="text-2xl font-bold {{ $pelanggan->has_piutang ? 'text-red-600' : 'text-green-600' }}">
                        {{ $pelanggan->total_piutang_format }}
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Jumlah Piutang</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ isset($piutangList) ? $piutangList->count() : 0 }} Transaksi
                    </p>
                </div>
            </div>
        </div>

        <!-- History Piutang -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history mr-2 text-purple-600"></i>
                    Riwayat Piutang
                </h3>
            </div>

            @if(isset($piutangList) && $piutangList->count() > 0)
                @foreach($piutangList as $piutang)
                    <div class="border-b border-gray-100 last:border-0">
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-3 py-1 text-xs font-bold rounded-lg bg-{{ $piutang->status_badge }}-100 text-{{ $piutang->status_badge }}-700 border border-{{ $piutang->status_badge }}-200">
                                            {{ $piutang->status_label }}
                                        </span>
                                        @if($piutang->isJatuhTempo())
                                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-red-100 text-red-700 border border-red-200">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Jatuh Tempo
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <i class="far fa-calendar mr-1"></i>
                                        {{ $piutang->tanggal_piutang->format('d M Y H:i') }}
                                    </p>
                                    @if($piutang->keterangan)
                                        <p class="text-xs text-gray-500 line-clamp-1">{{ $piutang->keterangan }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-800">{{ $piutang->total_piutang_format }}</p>
                                    @if($piutang->status_piutang != 'lunas')
                                        <p class="text-sm text-red-600 font-semibold">
                                            Sisa: {{ $piutang->sisa_piutang_format }}
                                        </p>
                                    @endif
                                    <a href="{{ route('piutang.show', $piutang->id_piutang) }}"
                                        class="inline-block mt-2 text-xs text-purple-600 hover:text-purple-700 font-semibold">
                                        Lihat Detail <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Progress Bar for Cicilan -->
                            @if($piutang->status_piutang == 'cicilan')
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Terbayar {{ number_format($piutang->persentase_terbayar, 1) }}%</span>
                                        <span>{{ $piutang->total_terbayar_format }} / {{ $piutang->total_piutang_format }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full transition-all"
                                            style="width: {{ $piutang->persentase_terbayar }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-file-invoice text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada riwayat piutang</p>
                    <p class="text-gray-400 text-sm mt-1">Piutang akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>
@endsection