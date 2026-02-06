@extends('layouts.app')

@section('title', 'Edit User')

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
                    <i class="fas fa-user-edit mr-2 text-yellow-600"></i>Edit User
                </h1>
                <p class="text-gray-600 mt-1">Perbarui data pengguna</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <form action="{{ route('users.update', $user->id_user) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Upload Foto Profil -->
                <div class="flex flex-col items-center pb-6 border-b border-gray-200">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg overflow-hidden"
                            id="preview-container">
                            @if ($user->gambar_user && file_exists(public_path('uploads/users/' . $user->gambar_user)))
                                <img src="{{ asset('uploads/users/' . $user->gambar_user) }}" id="image-preview"
                                    class="w-full h-full object-cover" alt="User Photo">
                                <i class="fas fa-user hidden" id="default-icon"></i>
                            @else
                                <i class="fas fa-user" id="default-icon"></i>
                                <img id="image-preview" class="w-full h-full object-cover hidden" alt="Preview">
                            @endif
                        </div>
                        <label for="gambar_user"
                            class="absolute bottom-0 right-0 bg-yellow-600 hover:bg-yellow-700 text-white p-3 rounded-full cursor-pointer shadow-lg transition hover:scale-110">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="gambar_user" name="gambar_user"
                            accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden">
                    </div>

                    <!-- Opsi Hapus Gambar -->
                    @if ($user->gambar_user && file_exists(public_path('uploads/users/' . $user->gambar_user)))
                        <label class="mt-3 flex items-center space-x-2 cursor-pointer group">
                            <input type="checkbox" name="hapus_gambar" value="1" id="hapus_gambar"
                                class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-sm text-gray-600 group-hover:text-red-600 transition">
                                <i class="fas fa-trash-alt mr-1"></i>Hapus foto profil
                            </span>
                        </label>
                    @endif

                    <p class="text-sm text-gray-500 mt-3 text-center">
                        <i class="fas fa-info-circle mr-1"></i>Upload foto profil baru (opsional)
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
                        <input type="text" name="nama_user" value="{{ old('nama_user', $user->nama_user) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition @error('nama_user') border-red-500 @enderror"
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
                        <input type="text" name="username_user" value="{{ old('username_user', $user->username_user) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition @error('username_user') border-red-500 @enderror"
                            placeholder="Masukkan username">
                        @error('username_user')
                            <p class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
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
                                {{ old('role_user', $user->role_user) == 'owner' ? 'checked' : '' }} class="peer sr-only"
                                required>
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
                                {{ old('role_user', $user->role_user) == 'kasir' ? 'checked' : '' }} class="peer sr-only"
                                required>
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

                <!-- Divider -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-bold text-gray-800 mb-2">
                        <i class="fas fa-key mr-2"></i>Ganti Password (Opsional)
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-info-circle mr-1"></i>Kosongkan jika tidak ingin mengubah password
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password_user"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition @error('password_user') border-red-500 @enderror"
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
                            <i class="fas fa-lock mr-1"></i>Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_user_confirmation"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition"
                                placeholder="Ulangi password baru">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition hover:scale-105 hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Update User
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold text-center transition hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl p-6 border border-yellow-200">
            <div class="flex items-start space-x-3">
                <div class="bg-yellow-100 p-3 rounded-lg flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 mb-2">Informasi Penting</h4>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Password lama akan tetap digunakan jika tidak diubah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Password baru minimal 6 karakter</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Username harus unik dan tidak boleh sama dengan user lain</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Upload foto baru untuk mengganti foto profil</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Centang "Hapus foto profil" untuk menghapus foto yang ada</span>
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
                const hapusGambarCheckbox = document.getElementById('hapus_gambar');

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

                        // Uncheck hapus gambar jika user upload foto baru
                        if (hapusGambarCheckbox) {
                            hapusGambarCheckbox.checked = false;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Handle Hapus Gambar Checkbox
            function handleHapusGambar() {
                const hapusGambarCheckbox = document.getElementById('hapus_gambar');
                const fileInput = document.getElementById('gambar_user');
                const preview = document.getElementById('image-preview');
                const defaultIcon = document.getElementById('default-icon');

                if (hapusGambarCheckbox && hapusGambarCheckbox.checked) {
                    // Clear file input
                    fileInput.value = '';

                    // Show default icon
                    preview.classList.add('hidden');
                    defaultIcon.classList.remove('hidden');
                    preview.src = '';
                }
            }

            // Auto-attach event listeners
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('gambar_user');
                const hapusGambarCheckbox = document.getElementById('hapus_gambar');

                if (fileInput) {
                    fileInput.addEventListener('change', previewImage);
                }

                if (hapusGambarCheckbox) {
                    hapusGambarCheckbox.addEventListener('change', handleHapusGambar);
                }
            });
        </script>
    @endpush
@endsection
