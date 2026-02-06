@extends('layouts.app')

@section('title', 'Laporan Piutang - Toko Sahabat')
@section('page-title', 'Laporan Piutang')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header with Back Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center">
                <a href="{{ route('piutang.index') }}"
                    class="mr-3 w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all">
                    <i class="fas fa-arrow-left text-white text-lg sm:text-xl"></i>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Laporan Piutang</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Ringkasan dan analisis piutang</p>
                </div>
            </div>
            <button onclick="window.print()"
                class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-lg text-sm">
                <i class="fas fa-print mr-2"></i>Cetak Laporan
            </button>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 no-print">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-600"></i>
                Filter Laporan
            </h3>
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Status</label>
                    <select name="status"
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="cicilan" {{ request('status') == 'cicilan' ? 'selected' : '' }}>Cicilan</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition-all text-sm">
                        <i class="fas fa-search mr-1"></i>Tampilkan
                    </button>
                    @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'status']))
                        <a href="{{ route('piutang.laporan') }}"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-all text-sm">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Total Piutang</p>
                    <p class="text-xl sm:text-2xl font-bold text-red-600">
                        Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Terbayar</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600">
                        Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Sisa Piutang</p>
                    <p class="text-xl sm:text-2xl font-bold text-orange-600">
                        Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Transaksi</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $summary['jumlah_transaksi'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Laporan Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-table mr-2 text-blue-600"></i>
                    Detail Laporan Piutang
                </h3>
                @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai']))
                    <p class="text-sm text-gray-600 mt-1">
                        Periode: 
                        @if(request('tanggal_dari'))
                            {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d M Y') }}
                        @else
                            Awal
                        @endif
                        s/d
                        @if(request('tanggal_sampai'))
                            {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d M Y') }}
                        @else
                            Sekarang
                        @endif
                    </p>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Pelanggan</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Terbayar</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Sisa</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($piutang as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item->tanggal_piutang->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-800">{{ $item->pelanggan->nama_pelanggan }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->user->nama_user }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    Rp {{ number_format($item->total_piutang, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-green-600">
                                    Rp {{ number_format($item->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold {{ $item->sisa_piutang > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-{{ $item->status_badge }}-100 text-{{ $item->status_badge }}-700">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Tidak ada data</p>
                                        <p class="text-gray-400 text-sm mt-1">Coba ubah filter</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($piutang->count() > 0)
                        <tfoot class="bg-gradient-to-r from-gray-100 to-gray-200">
                            <tr class="font-bold">
                                <td colspan="3" class="px-4 py-3 text-right text-gray-800">TOTAL:</td>
                                <td class="px-4 py-3 text-right text-gray-800">
                                    Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-green-600">
                                    Rp {{ number_format($summary['total_terbayar'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-red-600">
                                    Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Analisis -->
        @if($piutang->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-purple-600"></i>
                    Analisis Piutang
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <p class="text-sm text-green-700 mb-1">Persentase Lunas</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $piutang->where('status_piutang', 'lunas')->count() > 0 ? round(($piutang->where('status_piutang', 'lunas')->count() / $piutang->count()) * 100, 1) : 0 }}%
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ $piutang->where('status_piutang', 'lunas')->count() }} dari {{ $piutang->count() }} transaksi
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                        <p class="text-sm text-yellow-700 mb-1">Persentase Cicilan</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ $piutang->where('status_piutang', 'cicilan')->count() > 0 ? round(($piutang->where('status_piutang', 'cicilan')->count() / $piutang->count()) * 100, 1) : 0 }}%
                        </p>
                        <p class="text-xs text-yellow-600 mt-1">
                            {{ $piutang->where('status_piutang', 'cicilan')->count() }} transaksi
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                        <p class="text-sm text-red-700 mb-1">Belum Lunas</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $piutang->where('status_piutang', 'belum_lunas')->count() > 0 ? round(($piutang->where('status_piutang', 'belum_lunas')->count() / $piutang->count()) * 100, 1) : 0 }}%
                        </p>
                        <p class="text-xs text-red-600 mt-1">
                            {{ $piutang->where('status_piutang', 'belum_lunas')->count() }} transaksi
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Info -->
        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 text-center">
            <p>Laporan dicetak pada: <strong>{{ now()->format('d M Y H:i') }}</strong></p>
            <p class="text-xs mt-1">Toko Sahabat - Sistem Manajemen Piutang</p>
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }
                body {
                    print-color-adjust: exact;
                    -webkit-print-color-adjust: exact;
                }
            }
        </style>
    @endpush
@endsection