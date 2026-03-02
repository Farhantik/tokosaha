@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('users.index') }}"
                class="bg-white hover:bg-gray-100 text-gray-700 p-3 rounded-xl shadow-lg transition hover:scale-105">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-user-plus mr-2 text-emerald-600"></i>Tambah User
                </h1>
                <p class="text-gray-500 mt-1 text-sm">Buat akun pengguna baru</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-green-500"></div>
            <div class="p-6 md:p-8">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Upload Foto Profil -->
                    <div class="flex flex-col items-center pb-6 border-b border-gray-100">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg overflow-hidden"
                                id="preview-container">
                                <i class="fas fa-user" id="default-icon"></i>
                                <img id="image-preview" class="w-full h-full object-cover hidden" alt="Preview">
                            </div>
                            <label for="gambar_user"
                                class="absolute bottom-0 right-0 bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-full cursor-pointer shadow-lg transition hover:scale-110">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="gambar_user" name="gambar_user"
                                accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden">
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            <i class="fas fa-info-circle mr-1 text-emerald-500"></i>Upload foto profil (opsional)
                            <br>Format: JPG, PNG, GIF &nbsp;|&nbsp; Maks: 2MB
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
                                <i class="fas fa-user mr-1 text-emerald-500"></i>Nama Lengkap <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_user" value="{{ old('nama_user') }}" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm @error('nama_user') border-red-400 @enderror"
                                placeholder="Masukkan nama lengkap">
                            @error('nama_user')
                                <p class="text-red-500 text-xs mt-1.5"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-at mr-1 text-emerald-500"></i>Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username_user" value="{{ old('username_user') }}" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm @error('username_user') border-red-400 @enderror"
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

                        <!-- Password -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-1 text-emerald-500"></i>Password <span
                                    class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password_user" required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm @error('password_user') border-red-400 @enderror"
                                    placeholder="Min 8 karakter" oninput="checkStrength(this.value)">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                    <i class="fas fa-eye text-sm" id="password-icon"></i>
                                </button>
                            </div>

                            <!-- Password Strength Bar -->
                            <div class="mt-2">
                                <div class="flex gap-1 mb-1">
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar1"></div>
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar2"></div>
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar3"></div>
                                    <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="bar4"></div>
                                </div>
                                <p id="strength-text" class="text-xs text-gray-400"></p>
                            </div>

                            <!-- Syarat Password -->
                            <div class="mt-3 bg-gray-50 rounded-xl p-3 space-y-1.5" id="password-requirements">
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

                        <!-- Konfirmasi Password -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-1 text-emerald-500"></i>Konfirmasi Password <span
                                    class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_user_confirmation"
                                    required autocomplete="new-password"
                                    class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm"
                                    placeholder="Ulangi password" oninput="checkMatch()">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                    <i class="fas fa-eye text-sm" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            <p id="match-text" class="text-xs mt-1.5 hidden"></p>
                        </div>

                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-user-tag mr-1 text-emerald-500"></i>Role <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role_user" value="owner"
                                    {{ old('role_user') == 'owner' ? 'checked' : '' }} class="peer sr-only" required>
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-amber-500 peer-checked:bg-amber-50 transition hover:border-amber-300 hover:shadow-md">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="bg-amber-100 p-3 rounded-xl group-hover:scale-110 transition flex-shrink-0">
                                            <i class="fas fa-crown text-xl text-amber-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Owner</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Akses penuh</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role_user" value="kasir"
                                    {{ old('role_user') == 'kasir' ? 'checked' : '' }} class="peer sr-only" required>
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition hover:border-emerald-300 hover:shadow-md">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="bg-emerald-100 p-3 rounded-xl group-hover:scale-110 transition flex-shrink-0">
                                            <i class="fas fa-cash-register text-xl text-emerald-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Kasir</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Akses transaksi</p>
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

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" id="btnSubmit"
                            class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition hover:scale-[1.02] hover:shadow-xl text-sm">
                            <i class="fas fa-save mr-2"></i>Simpan User
                        </button>
                        <a href="{{ route('users.index') }}"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold text-center transition hover:scale-[1.02] text-sm">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-2xl p-5 border border-emerald-200">
            <div class="flex items-start space-x-3">
                <div class="bg-emerald-100 p-3 rounded-xl flex-shrink-0">
                    <i class="fas fa-shield-alt text-emerald-600 text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 mb-2 text-sm">Kebijakan Password</h4>
                    <ul class="text-sm text-gray-600 space-y-1.5">
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Minimal 8 karakter</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Wajib mengandung huruf besar, huruf kecil, angka, dan simbol</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Username hanya boleh huruf, angka, titik, underscore, atau strip</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Role Owner memiliki akses penuh ke semua fitur</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Foto profil opsional, format JPG/PNG/GIF maks 2MB</span>
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

                const checks = {
                    length: value.length >= 8,
                    upper: /[A-Z]/.test(value),
                    lower: /[a-z]/.test(value),
                    number: /[0-9]/.test(value),
                    symbol: /[^A-Za-z0-9]/.test(value),
                };

                // Update requirement checklist
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
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                    defaultIcon.classList.remove('hidden');
                    preview.src = '';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('gambar_user');
                if (fileInput) fileInput.addEventListener('change', previewImage);
            });
        </script>
    @endpush
@endsection
