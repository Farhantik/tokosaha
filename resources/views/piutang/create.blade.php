@extends('layouts.app')

@section('title', 'Catat Piutang - Toko Sahabat')
@section('page-title', 'Catat Piutang Baru')

@section('content')
    <div class="max-w-2xl mx-auto space-y-4 sm:space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center">
            <a href="{{ route('piutang.index') }}"
                class="mr-3 w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all">
                <i class="fas fa-arrow-left text-white text-lg sm:text-xl"></i>
            </a>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Catat Piutang Baru</h2>
                <p class="text-xs sm:text-sm text-gray-500">Tambah piutang pelanggan</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 bg-gradient-to-r from-red-500 to-pink-600 text-white">
                <h3 class="text-lg font-bold flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Form Piutang Baru
                </h3>
                <p class="text-sm opacity-90 mt-1">Isi data piutang dengan lengkap</p>
            </div>

            <form action="{{ route('piutang.store') }}" method="POST" class="p-4 sm:p-6 space-y-5">
                @csrf

                <!-- Pilih Pelanggan -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <select name="id_pelanggan" id="id_pelanggan" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm @error('id_pelanggan') border-red-500 @enderror">
                        <option value="">- Pilih Pelanggan -</option>
                        @foreach($pelanggan as $item)
                            <option value="{{ $item->id_pelanggan }}" {{ old('id_pelanggan') == $item->id_pelanggan ? 'selected' : '' }}>
                                {{ $item->nama_pelanggan }}
                                @if($item->has_piutang)
                                    (Piutang: {{ $item->total_piutang_format }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('id_pelanggan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        Belum ada pelanggan? 
                        <a href="{{ route('pelanggan.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold ml-1">
                            Tambah di sini
                        </a>
                    </p>
                </div>

                <!-- Jumlah Piutang -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Total Piutang <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="total_piutang" id="total_piutang" required min="1" step="0.01"
                            value="{{ old('total_piutang') }}"
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm @error('total_piutang') border-red-500 @enderror"
                            placeholder="100000">
                    </div>
                    @error('total_piutang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        Masukkan jumlah total piutang pelanggan
                    </p>
                </div>

                <!-- Jatuh Tempo -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Tanggal Jatuh Tempo
                    </label>
                    <input type="date" name="jatuh_tempo" id="jatuh_tempo" 
                        value="{{ old('jatuh_tempo') }}"
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm @error('jatuh_tempo') border-red-500 @enderror">
                    @error('jatuh_tempo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>
                        Opsional. Tentukan batas waktu pembayaran
                    </p>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Keterangan
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm @error('keterangan') border-red-500 @enderror"
                        placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold mb-1">Informasi Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Piutang akan tercatat atas nama pelanggan yang dipilih</li>
                                <li>Status awal piutang adalah "Belum Lunas"</li>
                                <li>Pembayaran dapat dilakukan secara bertahap (cicilan)</li>
                                <li>Kasir: <strong>{{ auth()->user()->nama_user }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-save mr-2"></i>Simpan Piutang
                    </button>
                    <a href="{{ route('piutang.index') }}"
                        class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] text-center">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-lg p-4">
            <h4 class="font-bold text-gray-800 mb-3 flex items-center text-sm">
                <i class="fas fa-chart-line mr-2 text-red-600"></i>
                Statistik Piutang Hari Ini
            </h4>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500 mb-1">Piutang Baru</p>
                    <p class="text-xl font-bold text-gray-800">
                        {{ \App\Models\Piutang::whereDate('tanggal_piutang', today())->count() }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500 mb-1">Total Hari Ini</p>
                    <p class="text-lg font-bold text-red-600">
                        Rp {{ number_format(\App\Models\Piutang::whereDate('tanggal_piutang', today())->sum('total_piutang'), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto format number input
            const totalPiutangInput = document.getElementById('total_piutang');
            
            totalPiutangInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(0);
                }
            });

            // Set default jatuh tempo (30 hari dari sekarang)
            const jatuhTempoInput = document.getElementById('jatuh_tempo');
            if (!jatuhTempoInput.value) {
                const defaultDate = new Date();
                defaultDate.setDate(defaultDate.getDate() + 30);
                jatuhTempoInput.value = defaultDate.toISOString().split('T')[0];
            }
        </script>
    @endpush
@endsection