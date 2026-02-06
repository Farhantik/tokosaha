@extends('layouts.app')

@section('title', 'Tambah User')

@section('page-title', 'Tambah User')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('users.index') }}"
                class="bg-white hover:bg-gray-100 text-gray-800 p-3 rounded-xl shadow-lg transition hover:scale-105">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-user-plus mr-2 text-purple-600"></i>Tambah User
                </h1>
                <p class="text-gray-600 mt-1">Buat akun pengguna baru</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Upload Foto Profil -->
                <div class="flex flex-col items-center pb-6 border-b border-gray-200">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg overflow-hidden"
                            id="preview-container">
                            <i class="fas fa-user" id="default-icon"></i>
                            <img id="image-preview" class="w-full h-full object-cover hidden" alt="Preview">
                        </div>
                        <label for="gambar_user"
                            class="absolute bottom-0 right-0 bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-full cursor-pointer shadow-lg transition hover:scale-110">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="gambar_user" name="gambar_user"
                            accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden">
                    </div>
                    <p class="text-sm text-gray-500 mt-3 text-center">
                        <i class="fas fa-info-circle mr-1"></i>Upload foto profil (opsional)
                        <br>Format: JPG, PNG, GIF | Max: 2MB
                    </p>
                    @error('gambar_user')
                        <p class="text-red-500 text-sm mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Nama User -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_user" value="{{ old('nama_user') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition @error('nama_user') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap">
                        @error('nama_user')
                            <p class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-at mr-1"></i>Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username_user" value="{{ old('username_user') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition @error('username_user') border-red-500 @enderror"
                            placeholder="Masukkan username">
                        @error('username_user')
                            <p class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password_user" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition @error('password_user') border-red-500 @enderror"
                                placeholder="Minimal 6 karakter">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password_user')
                            <p class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_user_confirmation" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Ulangi password">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-user-tag mr-1"></i>Role <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="role_user" value="owner"
                                {{ old('role_user') == 'owner' ? 'checked' : '' }} class="peer sr-only" required>
                            <div
                                class="p-4 border-2 border-gray-300 rounded-xl peer-checked:border-purple-600 peer-checked:bg-purple-50 transition hover:border-purple-400 hover:shadow-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-purple-100 p-3 rounded-lg group-hover:scale-110 transition">
                                        <i class="fas fa-crown text-xl text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">Owner</p>
                                        <p class="text-xs text-gray-600">Akses penuh</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="role_user" value="kasir"
                                {{ old('role_user') == 'kasir' ? 'checked' : '' }} class="peer sr-only" required>
                            <div
                                class="p-4 border-2 border-gray-300 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50 transition hover:border-blue-400 hover:shadow-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-100 p-3 rounded-lg group-hover:scale-110 transition">
                                        <i class="fas fa-user text-xl text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">Kasir</p>
                                        <p class="text-xs text-gray-600">Akses terbatas</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('role_user')
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition hover:scale-105 hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Simpan User
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold text-center transition hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 border border-blue-200">
            <div class="flex items-start space-x-3">
                <div class="bg-blue-100 p-3 rounded-lg flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 mb-2">Informasi Penting</h4>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Password minimal 6 karakter untuk keamanan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Username harus unik dan tidak boleh sama</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Role Owner memiliki akses penuh ke semua fitur</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Role Kasir hanya dapat mengakses transaksi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Foto profil opsional, format JPG/PNG/GIF max 2MB</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle Password Visibility
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '-icon');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // Preview Image
            function previewImage(event) {
                const file = event.target.files[0];
                const preview = document.getElementById('image-preview');
                const defaultIcon = document.getElementById('default-icon');

                if (file) {
                    // Validasi ukuran file (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB');
                        event.target.value = '';
                        return;
                    }

                    // Validasi tipe file
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert('Format file tidak valid! Gunakan JPG, PNG, atau GIF');
                        event.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        defaultIcon.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                    defaultIcon.classList.remove('hidden');
                    preview.src = '';
                }
            }

            // Auto-attach event listener ke input file
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('gambar_user');
                if (fileInput) {
                    fileInput.addEventListener('change', previewImage);
                }
            });
        </script>
    @endpush
@endsection
