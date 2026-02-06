@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

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
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-edit mr-3"></i>
                            Edit Supplier
                        </h2>
                        <p class="text-yellow-100 mt-1">ID Supplier: {{ $supplier->id_supplier }}</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('suppliers.update', $supplier->id_supplier) }}"
                        class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

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
                                    value="{{ old('nama_supplier', $supplier->nama_supplier) }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('nama_supplier') border-red-500 @enderror"
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
                                    value="{{ old('telp_supplier', $supplier->telp_supplier) }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('telp_supplier') border-red-500 @enderror"
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
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all resize-none @error('alamat_supplier') border-red-500 @enderror"
                                    placeholder="Masukkan alamat lengkap supplier">{{ old('alamat_supplier', $supplier->alamat_supplier) }}</textarea>
                            </div>
                            @error('alamat_supplier')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Update Supplier
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
                <!-- Supplier Info Card -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-lg p-6 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-info-circle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Info Supplier</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600 mb-1">ID Supplier</p>
                            <p class="font-semibold text-gray-900">{{ $supplier->id_supplier }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Total Penerimaan</p>
                            @php
                                $totalPenerimaan = DB::table('penerimaan')
                                    ->where('id_supplier', $supplier->id_supplier)
                                    ->count();
                            @endphp
                            <p class="font-semibold text-gray-900">
                                <i class="fas fa-box text-green-500 mr-1"></i>
                                {{ $totalPenerimaan }} kali
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Warning Card -->
                <div
                    class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-yellow-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Perhatian</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Perubahan data supplier akan <strong>mempengaruhi</strong> semua riwayat penerimaan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Pastikan data yang diisi <strong>sudah benar</strong> sebelum menyimpan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                            <span>Riwayat penerimaan <strong>tidak akan terhapus</strong> saat edit data</span>
                        </li>
                    </ul>
                </div>

                <!-- History Card -->
                <div
                    class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg p-6 border border-green-100">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white mr-3">
                            <i class="fas fa-history text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Riwayat</h3>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('suppliers.show', $supplier->id_supplier) }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Riwayat Penerimaan
                        </a>
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
