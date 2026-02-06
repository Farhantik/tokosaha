@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Supplier
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Form Tambah Supplier
                        </h2>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('suppliers.store') }}" class="p-6 space-y-6">
                        @csrf

                        <!-- Nama Supplier -->
                        <div>
                            <label for="nama_supplier" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Supplier <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                <input type="text" name="nama_supplier" id="nama_supplier"
                                    value="{{ old('nama_supplier') }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('nama_supplier') border-red-500 @enderror"
                                    placeholder="Contoh: PT. Sumber Jaya" required>
                            </div>
                            @error('nama_supplier')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="telp_supplier" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="text" name="telp_supplier" id="telp_supplier"
                                    value="{{ old('telp_supplier') }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('telp_supplier') border-red-500 @enderror"
                                    placeholder="Contoh: 08123456789">
                            </div>
                            @error('telp_supplier')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="alamat_supplier" class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-0 pl-4 pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <textarea name="alamat_supplier" id="alamat_supplier" rows="4"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none @error('alamat_supplier') border-red-500 @enderror"
                                    placeholder="Masukkan alamat lengkap supplier">{{ old('alamat_supplier') }}</textarea>
                            </div>
                            @error('alamat_supplier')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Supplier
                            </button>
                            <a href="{{ route('suppliers.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-200 text-center">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Section -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Help Card -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-lg p-6 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-info-circle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Informasi</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Nama supplier <strong>wajib diisi</strong> dan akan digunakan untuk identifikasi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Nomor telepon dan alamat bersifat <strong>opsional</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Data supplier dapat <strong>diedit</strong> kapan saja setelah disimpan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Supplier hanya bisa <strong>dihapus</strong> jika belum ada riwayat penerimaan</span>
                        </li>
                    </ul>
                </div>

                <!-- Tips Card -->
                <div
                    class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-yellow-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-lightbulb text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Tips</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Gunakan <strong>nama lengkap</strong> supplier untuk memudahkan pencarian</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Simpan <strong>nomor telepon</strong> untuk komunikasi yang lebih mudah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Alamat lengkap membantu dalam <strong>proses pengiriman</strong></span>
                        </li>
                    </ul>
                </div>

                <!-- Required Fields Card -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl shadow-lg p-6 border border-red-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-exclamation-circle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Field Wajib</h3>
                    </div>
                    <div class="space-y-2 text-sm text-gray-700">
                        <div class="flex items-center">
                            <span class="text-red-500 font-bold mr-2">*</span>
                            <span><strong>Nama Supplier</strong> - Wajib diisi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto format phone number
        document.getElementById('telp_supplier').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });
    </script>
@endpush
