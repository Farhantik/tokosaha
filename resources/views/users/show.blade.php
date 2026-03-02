@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('users.index') }}"
                    class="bg-white hover:bg-gray-100 text-gray-700 p-3 rounded-xl shadow-lg transition hover:scale-105">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        <i class="fas fa-user-circle mr-2 text-emerald-600"></i>Detail User
                    </h1>
                    <p class="text-gray-500 mt-1 text-sm">Informasi lengkap pengguna</p>
                </div>
            </div>
            @if (Auth::user()->id_user != $user->id_user)
                <a href="{{ route('users.edit', $user->id_user) }}"
                    class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-5 py-2.5 rounded-xl font-semibold transition hover:scale-105 shadow-lg text-sm flex items-center gap-2">
                    <i class="fas fa-edit"></i>Edit
                </a>
            @endif
        </div>

        <!-- User Hero Card -->
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                @if ($user->gambar_user && file_exists(public_path('storage/users/' . $user->gambar_user)))
                    <img src="{{ asset('storage/users/' . $user->gambar_user) }}" alt="{{ $user->nama_user }}"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white/30 shadow-2xl">
                @else
                    <div
                        class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-4xl font-bold shadow-xl border-4 border-white/30">
                        {{ strtoupper(substr($user->nama_user, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl font-bold mb-1">{{ $user->nama_user }}</h2>
                    <p class="text-white/75 mb-4 text-sm"><i class="fas fa-at mr-1"></i>{{ $user->username_user }}</p>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        @if ($user->role_user == 'owner')
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-crown mr-2 text-amber-300"></i>Owner
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-cash-register mr-2"></i>Kasir
                            </span>
                        @endif
                        @if (Auth::user()->id_user == $user->id_user)
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-user-circle mr-2"></i>Akun Anda
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-3 text-center">
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 min-w-[90px]">
                        <p class="text-3xl font-bold">{{ $stats['total_kasir'] }}</p>
                        <p class="text-xs text-white/75 mt-1">Sesi Kasir</p>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 min-w-[90px]">
                        <p class="text-3xl font-bold">{{ $stats['total_transaksi'] }}</p>
                        <p class="text-xs text-white/75 mt-1">Transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div
                class="bg-white rounded-2xl shadow-lg p-6 transition hover:shadow-xl hover:-translate-y-1 border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="bg-emerald-100 p-4 rounded-xl"><i
                            class="fas fa-cash-register text-2xl text-emerald-600"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Kasir Aktif</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['kasir_aktif'] }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-2xl shadow-lg p-6 transition hover:shadow-xl hover:-translate-y-1 border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 p-4 rounded-xl"><i class="fas fa-history text-2xl text-blue-500"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Sesi</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_kasir'] }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-2xl shadow-lg p-6 transition hover:shadow-xl hover:-translate-y-1 border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="bg-emerald-100 p-4 rounded-xl"><i
                            class="fas fa-shopping-cart text-2xl text-emerald-600"></i></div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Transaksi</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_transaksi'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Information -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>Informasi Detail
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl">
                        <div class="bg-gray-200 p-3 rounded-xl flex-shrink-0"><i class="fas fa-hashtag text-gray-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">ID User</p>
                            <p class="text-lg font-bold text-gray-800 mt-0.5">{{ $user->id_user }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 p-4 bg-emerald-50 rounded-xl">
                        <div class="bg-emerald-100 p-3 rounded-xl flex-shrink-0"><i
                                class="fas fa-user text-emerald-600"></i></div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Nama Lengkap</p>
                            <p class="text-lg font-bold text-gray-800 mt-0.5">{{ $user->nama_user }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 p-4 bg-emerald-50 rounded-xl">
                        <div class="bg-emerald-100 p-3 rounded-xl flex-shrink-0"><i class="fas fa-at text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Username</p>
                            <p class="text-lg font-bold text-gray-800 mt-0.5">{{ $user->username_user }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 p-4 bg-amber-50 rounded-xl">
                        <div class="bg-amber-100 p-3 rounded-xl flex-shrink-0"><i
                                class="fas fa-user-tag text-amber-600"></i></div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Role</p>
                            <p class="text-lg font-bold text-gray-800 mt-0.5 capitalize">{{ $user->role_user }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 p-4 bg-blue-50 rounded-xl">
                        <div class="bg-blue-100 p-3 rounded-xl flex-shrink-0"><i
                                class="fas fa-calendar-alt text-blue-500"></i></div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Terdaftar Sejak</p>
                            <p class="text-base font-bold text-gray-800 mt-0.5">
                                {{ date('d F Y, H:i', strtotime($user->created_at)) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl">
                        <div class="bg-gray-200 p-3 rounded-xl flex-shrink-0"><i
                                class="fas fa-circle-dot text-gray-600"></i></div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Status Kasir</p>
                            <div class="mt-1">
                                @if ($stats['kasir_aktif'] > 0)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-check-circle mr-1.5"></i>Kasir Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                        <i class="fas fa-minus-circle mr-1.5"></i>Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if (Auth::user()->id_user != $user->id_user)
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-emerald-600"></i>Aksi
                </h3>
                <div class="flex flex-wrap gap-3">
                    <button onclick="openResetPasswordModal()"
                        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold transition hover:scale-105 shadow-md text-sm">
                        <i class="fas fa-key"></i>Reset Password
                    </button>
                    <button onclick="confirmDelete()"
                        class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-semibold transition hover:scale-105 shadow-md text-sm">
                        <i class="fas fa-trash"></i>Hapus User
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- ===================== RESET PASSWORD MODAL ===================== -->
    <div id="resetPasswordModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="h-1.5 bg-gradient-to-r from-orange-400 to-red-500 rounded-t-2xl"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="bg-orange-100 p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-key text-xl text-orange-500"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Reset Password</h3>
                        <p class="text-sm text-gray-500">User: <span
                                class="font-semibold text-gray-700">{{ $user->nama_user }}</span></p>
                    </div>
                    <button onclick="closeResetPasswordModal()"
                        class="ml-auto text-gray-400 hover:text-gray-600 transition p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Password Baru -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-emerald-500"></i>Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="reset_new_password" autocomplete="new-password"
                                class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                                placeholder="Min 8 karakter" oninput="resetCheckStrength(this.value)">
                            <button type="button" onclick="toggleResetPassword('reset_new_password', 'icon_pass1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                <i class="fas fa-eye text-sm" id="icon_pass1"></i>
                            </button>
                        </div>

                        <!-- Strength Bar -->
                        <div class="mt-2">
                            <div class="flex gap-1 mb-1">
                                <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="rbar1"></div>
                                <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="rbar2"></div>
                                <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="rbar3"></div>
                                <div class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all" id="rbar4"></div>
                            </div>
                            <p id="rstrength-text" class="text-xs text-gray-400"></p>
                        </div>

                        <!-- Syarat Password -->
                        <div class="mt-2 bg-gray-50 rounded-xl p-3 hidden" id="reset-requirements">
                            <p class="text-xs font-semibold text-gray-600 mb-1.5">Syarat password:</p>
                            <div class="grid grid-cols-2 gap-1">
                                <p class="text-xs flex items-center gap-1.5" id="rreq-length"><i
                                        class="fas fa-circle text-gray-300 text-xs"></i>Min 8 karakter</p>
                                <p class="text-xs flex items-center gap-1.5" id="rreq-upper"><i
                                        class="fas fa-circle text-gray-300 text-xs"></i>Huruf besar (A-Z)</p>
                                <p class="text-xs flex items-center gap-1.5" id="rreq-lower"><i
                                        class="fas fa-circle text-gray-300 text-xs"></i>Huruf kecil (a-z)</p>
                                <p class="text-xs flex items-center gap-1.5" id="rreq-number"><i
                                        class="fas fa-circle text-gray-300 text-xs"></i>Angka (0-9)</p>
                                <p class="text-xs flex items-center gap-1.5 col-span-2" id="rreq-symbol"><i
                                        class="fas fa-circle text-gray-300 text-xs"></i>Simbol (!@#$%...)</p>
                            </div>
                        </div>

                        <p id="err_new_password" class="hidden text-red-500 text-xs mt-1.5">
                            <i class="fas fa-exclamation-circle mr-1"></i><span></span>
                        </p>
                    </div>

                    <!-- Konfirmasi (muncul setelah mengetik password) -->
                    <div id="reset-confirm-wrapper" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-emerald-500"></i>Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input type="password" id="reset_new_password_confirmation" autocomplete="new-password"
                                class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                                placeholder="Ulangi password baru" oninput="resetCheckMatch()">
                            <button type="button"
                                onclick="toggleResetPassword('reset_new_password_confirmation', 'icon_pass2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                <i class="fas fa-eye text-sm" id="icon_pass2"></i>
                            </button>
                        </div>
                        <p id="reset-match-text" class="text-xs mt-1.5 hidden"></p>
                        <p id="err_confirm_password" class="hidden text-red-500 text-xs mt-1.5">
                            <i class="fas fa-exclamation-circle mr-1"></i><span></span>
                        </p>
                    </div>

                    <!-- General Error -->
                    <div id="resetGeneralError"
                        class="hidden bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span id="resetGeneralErrorText"></span>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button id="btnResetSubmit" onclick="submitResetPassword()"
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-6 py-2.5 rounded-xl font-semibold transition hover:scale-[1.02] shadow-md text-sm">
                        <i class="fas fa-check mr-2" id="resetBtnIcon"></i>
                        <span id="resetBtnText">Reset Password</span>
                    </button>
                    <button type="button" onclick="closeResetPasswordModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-semibold transition hover:scale-[1.02] text-sm">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== DELETE MODAL ===================== -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="h-1.5 bg-gradient-to-r from-red-500 to-pink-500 rounded-t-2xl"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-red-100 p-3 rounded-xl flex-shrink-0"><i
                            class="fas fa-trash-alt text-xl text-red-500"></i></div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Konfirmasi Hapus</h3>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-sm text-gray-700">
                        Anda akan menghapus user: <strong class="text-red-600">{{ $user->nama_user }}</strong>
                    </p>
                </div>
                <form action="{{ route('users.destroy', $user->id_user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-6 py-2.5 rounded-xl font-semibold transition hover:scale-[1.02] shadow-md text-sm">
                            <i class="fas fa-trash mr-2"></i>Ya, Hapus
                        </button>
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-semibold transition hover:scale-[1.02] text-sm">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const _resetUserId = {{ $user->id_user }};

            // ================================================================
            // FLASH TOAST
            // ================================================================
            function _showFlashMessage(message, type = 'success') {
                const isSuccess = type === 'success';
                const el = document.createElement('div');
                el.style.cssText = 'opacity:0;transition:opacity 0.3s ease;';
                el.className =
                    `fixed top-5 right-5 z-[100] flex items-center gap-3 px-5 py-4 rounded-xl shadow-xl text-sm font-semibold ${isSuccess ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'}`;
                el.innerHTML =
                    `<i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
                document.body.appendChild(el);
                setTimeout(() => el.style.opacity = '1', 10);
                setTimeout(() => {
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 300);
                }, 3500);
            }

            // ================================================================
            // STRENGTH CHECKER
            // ================================================================
            function resetCheckStrength(value) {
                const bars = ['rbar1', 'rbar2', 'rbar3', 'rbar4'].map(id => document.getElementById(id));
                const txtEl = document.getElementById('rstrength-text');
                const reqBox = document.getElementById('reset-requirements');
                const confWrap = document.getElementById('reset-confirm-wrapper');

                if (value.length > 0) {
                    reqBox.classList.remove('hidden');
                    confWrap.classList.remove('hidden');
                } else {
                    reqBox.classList.add('hidden');
                    confWrap.classList.add('hidden');
                }

                const checks = {
                    length: value.length >= 8,
                    upper: /[A-Z]/.test(value),
                    lower: /[a-z]/.test(value),
                    number: /[0-9]/.test(value),
                    symbol: /[^A-Za-z0-9]/.test(value),
                };

                _resetUpdateReq('rreq-length', checks.length);
                _resetUpdateReq('rreq-upper', checks.upper);
                _resetUpdateReq('rreq-lower', checks.lower);
                _resetUpdateReq('rreq-number', checks.number);
                _resetUpdateReq('rreq-symbol', checks.symbol);

                const score = Object.values(checks).filter(Boolean).length;
                const colorMap = ['', 'bg-red-500', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-500'];
                const labelMap = ['', 'Sangat Lemah', 'Lemah', 'Sedang', 'Kuat'];
                const textColorMap = ['', 'text-red-500', 'text-orange-500', 'text-yellow-600', 'text-emerald-600'];

                bars.forEach((bar, i) => {
                    bar.className = 'h-1.5 flex-1 rounded-full transition-all ' + (i < score && score > 0 ? colorMap[
                        score] : 'bg-gray-200');
                });

                txtEl.textContent = value.length === 0 ? '' : (labelMap[score] || '');
                txtEl.className = 'text-xs font-semibold ' + (value.length === 0 ? 'text-gray-400' : (textColorMap[score] ||
                    'text-gray-400'));
                resetCheckMatch();
            }

            function _resetUpdateReq(id, passed) {
                const el = document.getElementById(id);
                if (!el) return;
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

            function resetCheckMatch() {
                const pass = document.getElementById('reset_new_password').value;
                const confirm = document.getElementById('reset_new_password_confirmation').value;
                const matchEl = document.getElementById('reset-match-text');
                if (!matchEl) return;
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

            // ================================================================
            // MODAL OPEN / CLOSE
            // ================================================================
            function openResetPasswordModal() {
                _clearResetForm();
                document.getElementById('resetPasswordModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => document.getElementById('reset_new_password').focus(), 100);
            }

            function closeResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
                _clearResetForm();
            }

            function _clearResetForm() {
                ['reset_new_password', 'reset_new_password_confirmation'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.value = '';
                        el.classList.remove('border-red-400');
                    }
                });
                _resetFieldType('reset_new_password', 'icon_pass1');
                _resetFieldType('reset_new_password_confirmation', 'icon_pass2');
                _hideError('err_new_password');
                _hideError('err_confirm_password');
                _hideGeneralError();
                document.getElementById('reset-requirements').classList.add('hidden');
                document.getElementById('reset-confirm-wrapper').classList.add('hidden');
                const matchEl = document.getElementById('reset-match-text');
                if (matchEl) matchEl.classList.add('hidden');
                ['rbar1', 'rbar2', 'rbar3', 'rbar4'].forEach(id => {
                    const bar = document.getElementById(id);
                    if (bar) bar.className = 'h-1.5 flex-1 rounded-full bg-gray-200 transition-all';
                });
                const txtEl = document.getElementById('rstrength-text');
                if (txtEl) {
                    txtEl.textContent = '';
                    txtEl.className = 'text-xs text-gray-400';
                }
                _setResetBtnLoading(false);
            }

            function _resetFieldType(fieldId, iconId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);
                if (field) field.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            function toggleResetPassword(fieldId, iconId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);
                if (!field || !icon) return;
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }

            // ================================================================
            // VALIDASI KLIEN
            // ================================================================
            function _validateResetForm(password, confirmation) {
                let valid = true;
                ['reset_new_password', 'reset_new_password_confirmation'].forEach(id => document.getElementById(id).classList
                    .remove('border-red-400'));
                _hideError('err_new_password');
                _hideError('err_confirm_password');
                _hideGeneralError();

                if (!password) {
                    _showError('err_new_password', 'Password baru harus diisi');
                    document.getElementById('reset_new_password').classList.add('border-red-400');
                    valid = false;
                } else if (password.length < 8) {
                    _showError('err_new_password', 'Password minimal 8 karakter');
                    document.getElementById('reset_new_password').classList.add('border-red-400');
                    valid = false;
                } else if (!/[A-Z]/.test(password)) {
                    _showError('err_new_password', 'Password harus mengandung huruf besar');
                    document.getElementById('reset_new_password').classList.add('border-red-400');
                    valid = false;
                } else if (!/[a-z]/.test(password)) {
                    _showError('err_new_password', 'Password harus mengandung huruf kecil');
                    document.getElementById('reset_new_password').classList.add('border-red-400');
                    valid = false;
                } else if (!/[0-9]/.test(password)) {
                    _showError('err_new_password', 'Password harus mengandung angka');
                    document.getElementById('reset_new_password').classList.add('border-red-400');
                    valid = false;
                } else if (!/[^A-Za-z0-9]/.test(password)) {
                    _showError('err_new_password', 'Password harus mengandung simbol (!@#$%...)');
                    document.getElementById('reset_new_password').classList.add('border-red-400');
                    valid = false;
                }

                if (!confirmation) {
                    _showError('err_confirm_password', 'Konfirmasi password wajib diisi');
                    document.getElementById('reset_new_password_confirmation').classList.add('border-red-400');
                    valid = false;
                } else if (password !== confirmation) {
                    _showError('err_confirm_password', 'Konfirmasi password tidak cocok');
                    document.getElementById('reset_new_password_confirmation').classList.add('border-red-400');
                    valid = false;
                }

                return valid;
            }

            async function submitResetPassword() {
                const password = document.getElementById('reset_new_password').value;
                const confirmation = document.getElementById('reset_new_password_confirmation').value;

                if (!_validateResetForm(password, confirmation)) return;

                _setResetBtnLoading(true);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        _showGeneralError('CSRF token tidak ditemukan. Silakan refresh halaman.');
                        return;
                    }

                    const response = await fetch(`/users/${_resetUserId}/reset-password`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            new_password: password,
                            new_password_confirmation: confirmation
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        closeResetPasswordModal();
                        _showFlashMessage('Password berhasil direset!', 'success');
                    } else if (response.status === 422) {
                        const errors = result.errors || {};
                        if (errors.new_password) {
                            _showError('err_new_password', errors.new_password[0]);
                            document.getElementById('reset_new_password').classList.add('border-red-400');
                        }
                        if (errors.new_password_confirmation) {
                            _showError('err_confirm_password', errors.new_password_confirmation[0]);
                            document.getElementById('reset_new_password_confirmation').classList.add('border-red-400');
                        }
                        if (!errors.new_password && !errors.new_password_confirmation) {
                            _showGeneralError(result.message || 'Validasi gagal.');
                        }
                    } else {
                        _showGeneralError(result.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                } catch (err) {
                    console.error('Reset password error:', err);
                    _showGeneralError('Koneksi gagal. Periksa jaringan Anda dan coba lagi.');
                } finally {
                    _setResetBtnLoading(false);
                }
            }

            // ================================================================
            // HELPERS
            // ================================================================
            function _showError(elId, msg) {
                const el = document.getElementById(elId);
                if (!el) return;
                el.querySelector('span').textContent = msg;
                el.classList.remove('hidden');
            }

            function _hideError(elId) {
                const el = document.getElementById(elId);
                if (el) el.classList.add('hidden');
            }

            function _showGeneralError(msg) {
                const el = document.getElementById('resetGeneralError');
                const txt = document.getElementById('resetGeneralErrorText');
                if (el && txt) {
                    txt.textContent = msg;
                    el.classList.remove('hidden');
                }
            }

            function _hideGeneralError() {
                const el = document.getElementById('resetGeneralError');
                if (el) el.classList.add('hidden');
            }

            function _setResetBtnLoading(isLoading) {
                const btn = document.getElementById('btnResetSubmit');
                const icon = document.getElementById('resetBtnIcon');
                const text = document.getElementById('resetBtnText');
                if (!btn) return;
                btn.disabled = isLoading;
                if (isLoading) {
                    icon.className = 'fas fa-spinner fa-spin mr-2';
                    text.textContent = 'Menyimpan...';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                } else {
                    icon.className = 'fas fa-check mr-2';
                    text.textContent = 'Reset Password';
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            }

            // ================================================================
            // DELETE MODAL
            // ================================================================
            function confirmDelete() {
                document.getElementById('deleteModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // ================================================================
            // EVENT LISTENERS
            // ================================================================
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    closeResetPasswordModal();
                    closeDeleteModal();
                }
            });
            document.getElementById('resetPasswordModal').addEventListener('click', e => {
                if (e.target === e.currentTarget) closeResetPasswordModal();
            });
            document.getElementById('deleteModal').addEventListener('click', e => {
                if (e.target === e.currentTarget) closeDeleteModal();
            });
            document.getElementById('resetPasswordModal').addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitResetPassword();
                }
            });
        </script>
    @endpush
@endsection
