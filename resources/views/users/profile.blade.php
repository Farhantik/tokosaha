@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-user-circle mr-2 text-emerald-600"></i>Profil Saya
            </h1>
            <p class="text-gray-600 mt-1">Kelola informasi akun Anda</p>
        </div>

        <!-- Profile Card -->
        <div class="bg-gradient-to-r from-emerald-600 to-green-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                <!-- Avatar -->
                @if ($user->gambar_user)
                    <img src="{{ asset('storage/users/' . $user->gambar_user) }}" alt="{{ $user->nama_user }}"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white/30 shadow-2xl"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full items-center justify-center text-4xl font-bold shadow-xl border-4 border-white/30"
                        style="display: none;">
                        {{ strtoupper(substr($user->nama_user, 0, 1)) }}
                    </div>
                @else
                    <div
                        class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-4xl font-bold shadow-xl border-4 border-white/30">
                        {{ strtoupper(substr($user->nama_user, 0, 1)) }}
                    </div>
                @endif

                <!-- Info -->
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl font-bold mb-2">{{ $user->nama_user }}</h2>
                    <p class="text-white/80 mb-3">
                        <i class="fas fa-at mr-1"></i>{{ $user->username_user }}
                    </p>

                    @if ($user->role_user == 'owner')
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-crown mr-2"></i>Owner
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-user mr-2"></i>Kasir
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-edit mr-2 text-emerald-600"></i>Edit Profil
            </h3>

            <form id="formUpdateProfile" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Upload Foto Profil -->
                <div class="flex flex-col items-center pb-6 border-b border-gray-200">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg overflow-hidden"
                            id="preview-container">
                            @if ($user->gambar_user)
                                <img src="{{ asset('storage/users/' . $user->gambar_user) }}" id="image-preview"
                                    class="w-full h-full object-cover" alt="User Photo"
                                    onerror="this.style.display='none'; document.getElementById('default-icon').style.display='block';">
                                <i class="fas fa-user hidden" id="default-icon"></i>
                            @else
                                <i class="fas fa-user" id="default-icon"></i>
                                <img id="image-preview" class="w-full h-full object-cover hidden" alt="Preview">
                            @endif
                        </div>
                        <label for="gambar_user"
                            class="absolute bottom-0 right-0 bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-full cursor-pointer shadow-lg transition hover:scale-110">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="gambar_user" name="gambar_user"
                            accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden">
                    </div>

                    <!-- Opsi Hapus Gambar -->
                    @if ($user->gambar_user)
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
                </div>

                <!-- Nama User -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i>Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_user" id="nama_user" value="{{ old('nama_user', $user->nama_user) }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-at mr-1"></i>Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="username_user" id="username_user"
                        value="{{ old('username_user', $user->username_user) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-bold text-gray-800 mb-2 flex items-center">
                        <i class="fas fa-key mr-2 text-emerald-600"></i>Ganti Password (Opsional)
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-info-circle mr-1"></i>Kosongkan jika tidak ingin mengubah password
                    </p>
                </div>

                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i>Password Lama
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Masukkan password lama">
                        <button type="button" onclick="togglePassword('current_password')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition">
                            <i class="fas fa-eye" id="current_password-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i>Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('new_password')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition">
                            <i class="fas fa-eye" id="new_password-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i>Konfirmasi Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePassword('new_password_confirmation')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition">
                            <i class="fas fa-eye" id="new_password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="pt-4">
                    <button type="submit" id="btnSubmit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-6 py-4 rounded-xl font-semibold shadow-lg transition hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Info -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-info-circle mr-2 text-emerald-600"></i>Informasi Akun
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User ID -->
                <div class="flex items-start space-x-3">
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <i class="fas fa-hashtag text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 font-medium">ID User</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $user->id_user }}</p>
                    </div>
                </div>

                <!-- Role -->
                <div class="flex items-start space-x-3">
                    <div class="bg-emerald-100 p-3 rounded-lg">
                        <i class="fas fa-user-tag text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Role</p>
                        <p class="text-lg font-semibold text-gray-800 capitalize">{{ $user->role_user }}</p>
                    </div>
                </div>

                <!-- Registered -->
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Terdaftar Sejak</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ date('d F Y', strtotime($user->created_at)) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-2xl p-6 border border-emerald-200">
            <div class="flex items-start space-x-3">
                <div class="bg-emerald-100 p-3 rounded-lg flex-shrink-0">
                    <i class="fas fa-lightbulb text-emerald-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 mb-2">Tips Keamanan</h4>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-600 mr-2 mt-0.5"></i>
                            <span>Gunakan password yang kuat dan unik</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-600 mr-2 mt-0.5"></i>
                            <span>Jangan bagikan password ke siapa pun</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-600 mr-2 mt-0.5"></i>
                            <span>Ganti password secara berkala</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-600 mr-2 mt-0.5"></i>
                            <span>Logout setelah selesai bekerja</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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

            function previewImage(event) {
                const file = event.target.files[0];
                const preview = document.getElementById('image-preview');
                const defaultIcon = document.getElementById('default-icon');
                const hapusGambarCheckbox = document.getElementById('hapus_gambar');

                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar!',
                            text: 'Ukuran file maksimal 2MB',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444',
                        });
                        event.target.value = '';
                        return;
                    }

                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format Tidak Valid!',
                            text: 'Gunakan format JPG, PNG, atau GIF',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444',
                        });
                        event.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        defaultIcon.classList.add('hidden');
                        if (hapusGambarCheckbox) {
                            hapusGambarCheckbox.checked = false;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }

            function handleHapusGambar() {
                const hapusGambarCheckbox = document.getElementById('hapus_gambar');
                const fileInput = document.getElementById('gambar_user');
                const preview = document.getElementById('image-preview');
                const defaultIcon = document.getElementById('default-icon');

                if (hapusGambarCheckbox && hapusGambarCheckbox.checked) {
                    fileInput.value = '';
                    preview.classList.add('hidden');
                    defaultIcon.classList.remove('hidden');
                    preview.src = '';
                }
            }

            document.getElementById('formUpdateProfile').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btnSubmit = document.getElementById('btnSubmit');
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('new_password_confirmation').value;

                if (newPassword || confirmPassword) {
                    if (newPassword !== confirmPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Tidak Cocok!',
                            text: 'Password baru dan konfirmasi password tidak sama',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444',
                        });
                        return;
                    }

                    if (newPassword.length < 6) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Terlalu Pendek!',
                            text: 'Password minimal 6 karakter',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444',
                        });
                        return;
                    }
                }

                const result = await Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: 'Pastikan semua data sudah benar sebelum menyimpan',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                });

                if (!result.isConfirmed) return;

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

                try {
                    const response = await fetch('{{ route('users.update-profile') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Profil berhasil diperbarui',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#10b981',
                        });
                        setTimeout(() => location.reload(), 500);
                    } else {
                        let errorMessage = data.message || 'Gagal memperbarui profil';
                        if (data.errors) {
                            errorMessage += '\n\n';
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => {
                                    errorMessage += '• ' + error + '\n';
                                });
                            });
                        }
                        throw new Error(errorMessage);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444',
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Perubahan';
                }
            });

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
