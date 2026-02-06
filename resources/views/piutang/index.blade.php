@extends('layouts.app')

@section('title', 'Piutang - Toko Sahabat')
@section('page-title', 'Manajemen Piutang')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-file-invoice-dollar text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manajemen Piutang</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola piutang pelanggan</p>
                </div>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('piutang.laporan') }}"
                    class="flex-1 sm:flex-initial bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-lg text-sm text-center">
                    <i class="fas fa-chart-bar mr-2"></i>Laporan
                </a>
                <a href="{{ route('piutang.create') }}"
                    class="flex-1 sm:flex-initial bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-lg text-sm text-center">
                    <i class="fas fa-plus mr-2"></i>Catat Piutang
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Total Piutang</p>
                        <p class="text-xl sm:text-2xl font-bold text-red-600">
                            Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-red-600 text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Belum Lunas</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['total_belum_lunas'] }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-gray-600 text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Cicilan</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['total_cicilan'] }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jatuh Tempo</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['total_jatuh_tempo'] }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <select name="status"
                    class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                    <option value="">Semua Status</option>
                    <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="cicilan" {{ request('status') == 'cicilan' ? 'selected' : '' }}>Cicilan</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
                <button type="submit"
                    class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all shadow-lg text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                @if(request()->hasAny(['status', 'jatuh_tempo']))
                    <a href="{{ route('piutang.index') }}"
                        class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all shadow-lg text-sm text-center">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @forelse($piutang as $item)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border-l-4 border-{{ $item->status_badge }}-500">
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($item->pelanggan->nama_pelanggan, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $item->pelanggan->nama_pelanggan }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $item->tanggal_piutang->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-{{ $item->status_badge }}-100 text-{{ $item->status_badge }}-700 border border-{{ $item->status_badge }}-200">
                                {{ $item->status_label }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total:</span>
                                <span class="font-bold text-gray-800">{{ $item->total_piutang_format }}</span>
                            </div>
                            @if($item->status_piutang != 'lunas')
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Sisa:</span>
                                    <span class="font-bold text-red-600">{{ $item->sisa_piutang_format }}</span>
                                </div>
                            @endif
                            @if($item->jatuh_tempo)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Jatuh Tempo:</span>
                                    <span class="text-sm {{ $item->is_jatuh_tempo ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                        {{ $item->jatuh_tempo->format('d M Y') }}
                                        @if($item->is_jatuh_tempo)
                                            <i class="fas fa-exclamation-triangle ml-1"></i>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($item->status_piutang == 'cicilan')
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Terbayar {{ $item->persentase_terbayar }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full"
                                        style="width: {{ $item->persentase_terbayar }}%"></div>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('piutang.show', $item->id_piutang) }}"
                            class="block w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg text-center">
                            <i class="fas fa-eye mr-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-invoice-dollar text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada piutang</p>
                    <p class="text-gray-400 text-sm mt-1">Catat piutang untuk memulai</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Pelanggan</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Sisa</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($piutang as $item)
                            <tr class="hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item->tanggal_piutang->format('d M Y') }}
                                    <div class="text-xs text-gray-400">{{ $item->tanggal_piutang->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                            {{ strtoupper(substr($item->pelanggan->nama_pelanggan, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $item->pelanggan->nama_pelanggan }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->user->nama_user }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-800">
                                    {{ $item->total_piutang_format }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold {{ $item->sisa_piutang > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item->sisa_piutang_format }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                    @if($item->jatuh_tempo)
                                        <span class="{{ $item->is_jatuh_tempo ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                            {{ $item->jatuh_tempo->format('d M Y') }}
                                            @if($item->is_jatuh_tempo)
                                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $item->status_badge }}-100 text-{{ $item->status_badge }}-700 border border-{{ $item->status_badge }}-200">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('piutang.show', $item->id_piutang) }}"
                                        class="inline-flex items-center bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
                                            <i class="fas fa-file-invoice-dollar text-4xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada piutang</p>
                                        <p class="text-gray-400 text-sm mt-1">Catat piutang untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $piutang->links() }}
            </div>
        </div>

        <!-- Pagination Mobile -->
        <div class="block lg:hidden">
            @if ($piutang->hasPages())
                <div class="bg-white rounded-xl shadow-lg p-4">
                    {{ $piutang->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
