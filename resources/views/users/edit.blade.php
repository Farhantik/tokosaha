@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

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
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-yellow-400 to-orange-500"></div>
            <div class="p-6 md:p-8">
                <form action="{{ route('users.update', $user->id_user) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Upload Foto Profil -->
                    <div class="flex flex-col items-center pb-6 border-b border-gray-200">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg overflow-hidden"
                                id="preview-container">
                                @if ($user->gambar_user)
                                    <img src="{{ asset('storage/users/' . $user->gambar_user) }}" id="image-preview"
                                        class="w-full h-full object-cover" alt="User Photo"
                                        onerror="this.style.display='none'; document.getElementById('default-icon').style.display='flex';">
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
                        @error('gambar_user')
                            <p class="text-red-500 text-sm mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">

                        <!-- Nama User -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i>Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_user" value="{{ old('nama_user', $user->nama_user) }}" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition text-sm @error('nama_user') border-red-400 @enderror"
                                placeholder="Masukkan nama lengkap">
                            @error('nama_user')
                                <p class="text-red-500 text-xs mt-1.5"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-at mr-1"></i>Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username_user"
                                value="{{ old('username_user', $user->username_user) }}" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition text-sm @error('username_user') border-red-400 @enderror"
                                placeholder="Hanya huruf, angka, titik, _ atau -">
                            <p class="text-gray-400 text-xs mt-1.5 flex items-center gap-1">
                                <i class="fas fa-info-circle text-blue-400"></i>
                                Hanya boleh huruf, angka, titik (.), underscore (_), atau strip (-)
                            </p>
                            @error('username_user')
                                <p class="text-red-500 text-xs mt-1.5"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
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
                                    {{ old('role_user', $user->role_user) == 'owner' ? 'checked' : '' }}
                                    class="peer sr-only" required>
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-purple-600 peer-checked:bg-purple-50 transition hover:border-purple-400 hover:shadow-md">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-purple-100 p-3 rounded-lg group-hover:scale-110 transition">
                                            <i class="fas fa-crown text-xl text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Owner</p>
                                            <p class="text-xs text-gray-500">Akses penuh</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role_user" value="kasir"
                                    {{ old('role_user', $user->role_user) == 'kasir' ? 'checked' : '' }}
                                    class="peer sr-only" required>
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50 transition hover:border-blue-400 hover:shadow-md">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-blue-100 p-3 rounded-lg group-hover:scale-110 transition">
                                            <i class="fas fa-cash-register text-xl text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Kasir</p>
                                            <p class="text-xs text-gray-500">Akses transaksi</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('role_user')
                            <p class="text-red-500 text-xs mt-1.5"><i
                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ganti Password (Opsional) -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-9 h-9 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-key text-yellow-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">Ganti Password <span
                                        class="text-gray-400 font-normal">(Opsional)</span></h3>
                                <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-1"></i>Password Baru
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password_user" autocomplete="new-password"
                                    class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition text-sm @error('password_user') border-red-400 @enderror"
                                    placeholder="Min 8 karakter" oninput="checkStrength(this.value)">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                    <i class="fas fa-eye text-sm" id="password-icon"></i>
                                </button>
                            </div>

                            <!-- Password Strength Bar -->
                            <div class="mt-2" id="strength-container">
                                <div class="flex gap-1 mb-1">
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar1">
                                    </div>
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar2">
                                    </div>
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar3">
                                    </div>
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar4">
                                    </div>
                                </div>
                                <p id="strength-text" class="text-xs text-gray-400"></p>
                            </div>

                            <!-- Syarat Password (muncul saat diketik) -->
                            <div class="mt-3 bg-gray-50 rounded-xl p-3 space-y-1.5 hidden" id="password-requirements">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Syarat password:</p>
                                <div class="grid grid-cols-2 gap-1">
                                    <p class="text-xs flex items-center gap-1.5" id="req-length">
                                        <i class="fas fa-circle text-gray-300 text-xs"></i>Minimal 8 karakter
                                    </p>
                                    <p class="text-xs flex items-center gap-1.5" id="req-upper">
                                        <i class="fas fa-circle text-gray-300 text-xs"></i>Huruf besar (A-Z)
                                    </p>
                                    <p class="text-xs flex items-center gap-1.5" id="req-lower">
                                        <i class="fas fa-circle text-gray-300 text-xs"></i>Huruf kecil (a-z)
                                    </p>
                                    <p class="text-xs flex items-center gap-1.5" id="req-number">
                                        <i class="fas fa-circle text-gray-300 text-xs"></i>Angka (0-9)
                                    </p>
                                    <p class="text-xs flex items-center gap-1.5 col-span-2" id="req-symbol">
                                        <i class="fas fa-circle text-gray-300 text-xs"></i>Simbol (!@#$%^&*...)
                                    </p>
                                </div>
                            </div>

                            @error('password_user')
                                <p class="text-red-500 text-xs mt-1.5"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div id="confirm-wrapper" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-1"></i>Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_user_confirmation"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition text-sm"
                                    placeholder="Ulangi password baru" oninput="checkMatch()">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                    <i class="fas fa-eye text-sm" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            <p id="match-text" class="text-xs mt-1.5 hidden"></p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition hover:scale-[1.02] hover:shadow-xl text-sm">
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                        <a href="{{ route('users.index') }}"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-xl font-semibold text-center transition hover:scale-[1.02] text-sm">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl p-5 border border-yellow-200">
            <div class="flex items-start space-x-3">
                <div class="bg-yellow-100 p-3 rounded-xl flex-shrink-0">
                    <i class="fas fa-shield-alt text-yellow-600 text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 mb-2 text-sm">Kebijakan Password</h4>
                    <ul class="text-sm text-gray-700 space-y-1.5">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Password lama tetap digunakan jika tidak diubah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Password baru minimal 8 karakter</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Wajib ada huruf besar, huruf kecil, angka, dan simbol</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                            <span>Username hanya boleh huruf, angka, titik, underscore, atau strip</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ── Toggle Password Visibility ──
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '-icon');
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }

            // ── Password Strength Checker ──
            function checkStrength(value) {
                const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'),
                    document.getElementById('bar4')
                ];
                const strengthTxt = document.getElementById('strength-text');
                const reqBox = document.getElementById('password-requirements');
                const confirmWrap = document.getElementById('confirm-wrapper');

                // Tampilkan/sembunyikan syarat & konfirmasi
                if (value.length > 0) {
                    reqBox.classList.remove('hidden');
                    confirmWrap.classList.remove('hidden');
                } else {
                    reqBox.classList.add('hidden');
                    confirmWrap.classList.add('hidden');
                }

                const checks = {
                    length: value.length >= 8,
                    upper: /[A-Z]/.test(value),
                    lower: /[a-z]/.test(value),
                    number: /[0-9]/.test(value),
                    symbol: /[^A-Za-z0-9]/.test(value),
                };

                updateReq('req-length', checks.length);
                updateReq('req-upper', checks.upper);
                updateReq('req-lower', checks.lower);
                updateReq('req-number', checks.number);
                updateReq('req-symbol', checks.symbol);

                const score = Object.values(checks).filter(Boolean).length;

                const colorMap = ['', 'bg-red-500', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-500'];
                const labelMap = ['', 'Sangat Lemah', 'Lemah', 'Sedang', 'Kuat'];
                const textColorMap = ['', 'text-red-500', 'text-orange-500', 'text-yellow-600', 'text-emerald-600'];

                bars.forEach((bar, i) => {
                    bar.className = 'h-1.5 flex-1 rounded-full transition-all ' +
                        (i < score && score > 0 ? colorMap[score] : 'bg-gray-200');
                });

                if (value.length === 0) {
                    strengthTxt.textContent = '';
                    strengthTxt.className = 'text-xs text-gray-400';
                } else {
                    strengthTxt.textContent = labelMap[score] || '';
                    strengthTxt.className = 'text-xs font-semibold ' + (textColorMap[score] || 'text-gray-400');
                }

                checkMatch();
            }

            function updateReq(id, passed) {
                const el = document.getElementById(id);
                const icon = el.querySelector('i');
                if (passed) {
                    icon.className = 'fas fa-check-circle text-emerald-500 text-xs';
                    el.classList.add('text-emerald-600');
                    el.classList.remove('text-gray-500');
                } else {
                    icon.className = 'fas fa-circle text-gray-300 text-xs';
                    el.classList.remove('text-emerald-600');
                    el.classList.add('text-gray-500');
                }
            }

            // ── Confirm Password Match ──
            function checkMatch() {
                const pass = document.getElementById('password').value;
                const confirm = document.getElementById('password_confirmation').value;
                const matchEl = document.getElementById('match-text');

                if (confirm.length === 0) {
                    matchEl.classList.add('hidden');
                    return;
                }
                matchEl.classList.remove('hidden');
                if (pass === confirm) {
                    matchEl.textContent = '✓ Password cocok';
                    matchEl.className = 'text-xs mt-1.5 text-emerald-600 font-semibold';
                } else {
                    matchEl.textContent = '✗ Password tidak cocok';
                    matchEl.className = 'text-xs mt-1.5 text-red-500 font-semibold';
                }
            }

            // ── Image Preview ──
            function previewImage(event) {
                const file = event.target.files[0];
                const preview = document.getElementById('image-preview');
                const defaultIcon = document.getElementById('default-icon');
                const hapusGambarCheck = document.getElementById('hapus_gambar');

                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB');
                        event.target.value = '';
                        return;
                    }
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
                        if (hapusGambarCheck) hapusGambarCheck.checked = false;
                    };
                    reader.readAsDataURL(file);
                }
            }

            function handleHapusGambar() {
                const hapusGambarCheck = document.getElementById('hapus_gambar');
                const fileInput = document.getElementById('gambar_user');
                const preview = document.getElementById('image-preview');
                const defaultIcon = document.getElementById('default-icon');

                if (hapusGambarCheck && hapusGambarCheck.checked) {
                    fileInput.value = '';
                    preview.classList.add('hidden');
                    defaultIcon.classList.remove('hidden');
                    preview.src = '';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('gambar_user');
                const hapusGambarCheck = document.getElementById('hapus_gambar');
                if (fileInput) fileInput.addEventListener('change', previewImage);
                if (hapusGambarCheck) hapusGambarCheck.addEventListener('change', handleHapusGambar);
            });
        </script>
    @endpush
@endsection
