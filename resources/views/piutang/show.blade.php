@extends('layouts.app')

@section('title', 'Detail Piutang - Toko Sahabat')
@section('page-title', 'Detail Piutang')

@section('content')
    <div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">
        <!-- Header with Back Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center">
                <a href="{{ route('piutang.index') }}"
                    class="mr-3 w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all">
                    <i class="fas fa-arrow-left text-white text-lg sm:text-xl"></i>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Piutang</h2>
                    <p class="text-xs sm:text-sm text-gray-500">ID: #{{ $piutang->id_piutang }}</p>
                </div>
            </div>
            @if($piutang->status_piutang != 'lunas' && $piutang->pembayaran->count() == 0)
                <form action="{{ route('piutang.destroy', $piutang->id_piutang) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus piutang ini?')"
                        class="w-full sm:w-auto bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-4 py-2 rounded-xl font-semibold transition-all shadow-lg text-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </form>
            @endif
        </div>

        <!-- Piutang Info Card -->
        <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-sm opacity-90 mb-1">Pelanggan</p>
                    <h3 class="text-2xl font-bold">{{ $piutang->pelanggan->nama_pelanggan }}</h3>
                    @if($piutang->pelanggan->no_telp)
                        <p class="text-sm opacity-90 mt-1">
                            <i class="fas fa-phone mr-1"></i>{{ $piutang->pelanggan->no_telp }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-white bg-opacity-20 rounded-lg text-sm font-semibold">
                        {{ $piutang->status_label }}
                    </span>
                    @if($piutang->is_jatuh_tempo)
                        <div class="mt-2 px-3 py-1 bg-yellow-500 rounded-lg text-xs font-bold">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Jatuh Tempo
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-xs opacity-75 mb-1">Total Piutang</p>
                    <p class="text-lg sm:text-xl font-bold">{{ $piutang->total_piutang_format }}</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-xs opacity-75 mb-1">Terbayar</p>
                    <p class="text-lg sm:text-xl font-bold">{{ $piutang->total_terbayar_format }}</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-xs opacity-75 mb-1">Sisa</p>
                    <p class="text-lg sm:text-xl font-bold">{{ $piutang->sisa_piutang_format }}</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-xs opacity-75 mb-1">Progress</p>
                    <p class="text-lg sm:text-xl font-bold">{{ $piutang->persentase_terbayar }}%</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="w-full bg-white bg-opacity-20 rounded-full h-3">
                    <div class="bg-white h-3 rounded-full transition-all duration-500"
                        style="width: {{ $piutang->persentase_terbayar }}%"></div>
                </div>
            </div>

            <!-- Info Tambahan -->
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="opacity-75">Tanggal Piutang</p>
                    <p class="font-semibold">{{ $piutang->tanggal_piutang->format('d M Y H:i') }}</p>
                </div>
                @if($piutang->jatuh_tempo)
                    <div>
                        <p class="opacity-75">Jatuh Tempo</p>
                        <p class="font-semibold {{ $piutang->is_jatuh_tempo ? 'text-yellow-300' : '' }}">
                            {{ $piutang->jatuh_tempo->format('d M Y') }}
                        </p>
                    </div>
                @endif
            </div>

            @if($piutang->keterangan)
                <div class="mt-4 p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                    <p class="text-xs opacity-75 mb-1">Keterangan</p>
                    <p class="text-sm">{{ $piutang->keterangan }}</p>
                </div>
            @endif
        </div>

        <!-- Form Pembayaran (Only if not Lunas) -->
        @if($piutang->status_piutang != 'lunas')
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                    <h3 class="text-lg font-bold flex items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Terima Pembayaran
                    </h3>
                    <p class="text-sm opacity-90 mt-1">Catat pembayaran piutang</p>
                </div>

                <form action="{{ route('piutang.bayar', $piutang->id_piutang) }}" method="POST" class="p-4 sm:p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Jumlah Bayar <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                            <input type="number" name="jumlah_bayar" id="jumlah_bayar" required min="1" 
                                max="{{ $piutang->sisa_piutang }}" step="0.01"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                placeholder="Maksimal: {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Sisa piutang: <strong>{{ $piutang->sisa_piutang_format }}</strong>
                        </p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="metode_pembayaran" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="e-wallet">E-Wallet</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">
                            Keterangan
                        </label>
                        <textarea name="keterangan" rows="2"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                            placeholder="Catatan pembayaran (opsional)"></textarea>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setAmount({{ $piutang->sisa_piutang }})"
                            class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-semibold transition-all">
                            Lunas ({{ $piutang->sisa_piutang_format }})
                        </button>
                        @if($piutang->sisa_piutang >= 100000)
                            <button type="button" onclick="setAmount(100000)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-all">
                                Rp 100.000
                            </button>
                        @endif
                        @if($piutang->sisa_piutang >= 50000)
                            <button type="button" onclick="setAmount(50000)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-all">
                                Rp 50.000
                            </button>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-check-circle mr-2"></i>Simpan Pembayaran
                    </button>
                </form>
            </div>
        @endif

        <!-- History Pembayaran -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history mr-2 text-blue-600"></i>
                    Riwayat Pembayaran
                </h3>
            </div>

            @forelse($piutang->pembayaran as $bayar)
                <div class="border-b border-gray-100 last:border-0">
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-{{ $bayar->metode_badge }}-100 text-{{ $bayar->metode_badge }}-700 border border-{{ $bayar->metode_badge }}-200">
                                        {{ $bayar->metode_label }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <i class="far fa-calendar mr-1"></i>
                                        {{ $bayar->tanggal_bayar->format('d M Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    Diterima oleh: <strong>{{ $bayar->user->nama_user }}</strong>
                                </p>
                                @if($bayar->keterangan)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $bayar->keterangan }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-green-600">{{ $bayar->jumlah_bayar_format }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-receipt text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada pembayaran</p>
                    <p class="text-gray-400 text-sm mt-1">Pembayaran akan muncul di sini</p>
                </div>
            @endforelse
        </div>

        <!-- Info Kasir & Tanggal -->
        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Dicatat Oleh</p>
                    <p class="font-semibold text-gray-800">{{ $piutang->user->nama_user }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                    <p class="font-semibold text-gray-800">{{ $piutang->pembayaran->count() }}x</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function setAmount(amount) {
                document.getElementById('jumlah_bayar').value = amount;
            }
        </script>
    @endpush
@endsection