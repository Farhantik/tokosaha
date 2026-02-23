@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Kelola User</h1>
                    <p class="text-xs sm:text-sm text-gray-600">Manajemen pengguna sistem</p>
                </div>
            </div>
            <a href="{{ route('users.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah User
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 text-lg flex-shrink-0"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="bg-red-50 border border-red-300 text-red-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle text-red-500 text-lg flex-shrink-0"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6">
            <form method="GET" action="{{ route('users.index') }}" class="space-y-3 sm:space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <!-- Search -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-search mr-1 text-emerald-600"></i>Pencarian
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama atau username..."
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm">
                    </div>

                    <!-- Filter Role -->
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-filter mr-1 text-emerald-600"></i>Role
                        </label>
                        <select name="role"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm">
                            <option value="">Semua Role</option>
                            <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="kasir" {{ request('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] text-center text-sm">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @forelse($users as $index => $user)
                <div
                    class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-gray-200">
                    <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-green-500"></div>
                    <div class="p-4">
                        <!-- Header -->
                        <div class="flex items-start gap-3 mb-3">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if ($user->gambar_user)
                                    <img src="{{ asset('storage/users/' . $user->gambar_user) }}"
                                        alt="{{ $user->nama_user }}"
                                        class="w-14 h-14 rounded-full object-cover border-2 border-emerald-200 shadow-md">
                                @else
                                    <div
                                        class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-md">
                                        {{ strtoupper(substr($user->nama_user, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">
                                            {{ $user->nama_user }}
                                        </h3>
                                        <p class="text-xs text-gray-500 break-all">
                                            &#64;{{ $user->username_user }}
                                        </p>
                                    </div>
                                    @if (Auth::user()->id_user == $user->id_user)
                                        <span
                                            class="flex-shrink-0 inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold whitespace-nowrap">
                                            <i class="fas fa-user-circle mr-1"></i>Anda
                                        </span>
                                    @endif
                                </div>

                                <!-- Role Badge -->
                                <div class="mt-2">
                                    @if ($user->role_user == 'owner')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            <i class="fas fa-crown mr-1"></i>Owner
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-cash-register mr-1"></i>Kasir
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Registration Date -->
                        <div class="flex items-center text-xs text-gray-500 mb-3 pb-3 border-b border-gray-200">
                            <i class="fas fa-calendar-alt mr-1.5 flex-shrink-0 text-emerald-500"></i>
                            <span>Terdaftar: {{ date('d/m/Y', strtotime($user->created_at)) }}</span>
                        </div>

                        <!-- Action Buttons -->
                        @if (Auth::user()->id_user != $user->id_user)
                            <div class="grid grid-cols-4 gap-2">
                                <a href="{{ route('users.show', $user->id_user) }}"
                                    class="flex flex-col items-center justify-center px-2 py-2.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all">
                                    <i class="fas fa-eye text-base mb-1"></i>
                                    <span class="text-xs font-semibold">Detail</span>
                                </a>
                                <a href="{{ route('users.edit', $user->id_user) }}"
                                    class="flex flex-col items-center justify-center px-2 py-2.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all">
                                    <i class="fas fa-edit text-base mb-1"></i>
                                    <span class="text-xs font-semibold">Edit</span>
                                </a>
                                <button
                                    onclick="openResetPasswordModal({{ $user->id_user }}, '{{ addslashes($user->nama_user) }}')"
                                    class="flex flex-col items-center justify-center px-2 py-2.5 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-all">
                                    <i class="fas fa-key text-base mb-1"></i>
                                    <span class="text-xs font-semibold">Reset</span>
                                </button>
                                <button
                                    onclick="confirmDelete({{ $user->id_user }}, '{{ addslashes($user->nama_user) }}')"
                                    class="flex flex-col items-center justify-center px-2 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all">
                                    <i class="fas fa-trash text-base mb-1"></i>
                                    <span class="text-xs font-semibold">Hapus</span>
                                </button>
                            </div>
                        @else
                            <div class="text-center py-2.5 bg-emerald-50 rounded-lg">
                                <span class="text-xs text-emerald-600 italic font-medium">
                                    <i class="fas fa-user-circle mr-1"></i>Ini adalah akun Anda
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-4xl text-emerald-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">Tidak ada data user</p>
                    @if (request('search') || request('role'))
                        <a href="{{ route('users.index') }}"
                            class="inline-block mt-3 text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                            <i class="fas fa-redo mr-1"></i>Reset Filter
                        </a>
                    @endif
                </div>
            @endforelse

            <!-- Mobile Pagination -->
            @if ($users->hasPages())
                <div class="bg-white rounded-xl shadow-lg p-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Desktop: Table View -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-emerald-500 to-green-600 text-white">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider">No</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider">Foto</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider">Username</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider">Terdaftar</th>
                            <th class="px-4 py-3.5 text-center text-xs font-bold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-emerald-50 transition-colors duration-150">
                                <td class="px-4 py-4 text-sm text-gray-700 font-medium">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-4">
                                    @if ($user->gambar_user)
                                        <img src="{{ asset('storage/users/' . $user->gambar_user) }}"
                                            alt="{{ $user->nama_user }}"
                                            class="w-11 h-11 rounded-full object-cover border-2 border-emerald-200 shadow-sm">
                                    @else
                                        <div
                                            class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-base shadow-sm">
                                            {{ strtoupper(substr($user->nama_user, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $user->nama_user }}</p>
                                        @if (Auth::user()->id_user == $user->id_user)
                                            <span class="text-xs text-emerald-600 font-medium">
                                                <i class="fas fa-user-circle mr-0.5"></i>Anda
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <span class="text-gray-600 font-medium">{{ $user->username_user }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($user->role_user == 'owner')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            <i class="fas fa-crown mr-1"></i>Owner
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-cash-register mr-1"></i>Kasir
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-calendar-alt text-emerald-400"></i>
                                        {{ date('d/m/Y', strtotime($user->created_at)) }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('users.show', $user->id_user) }}"
                                            class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-all"
                                            title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        @if (Auth::user()->id_user != $user->id_user)
                                            <a href="{{ route('users.edit', $user->id_user) }}"
                                                class="p-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg transition-all"
                                                title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                            <button
                                                onclick="openResetPasswordModal({{ $user->id_user }}, '{{ addslashes($user->nama_user) }}')"
                                                class="p-2 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg transition-all"
                                                title="Reset Password">
                                                <i class="fas fa-key text-sm"></i>
                                            </button>
                                            <button
                                                onclick="confirmDelete({{ $user->id_user }}, '{{ addslashes($user->nama_user) }}')"
                                                class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-all"
                                                title="Hapus">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        @else
                                            <span class="text-xs text-emerald-500 italic px-2 font-medium">Akun Anda</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div
                                            class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-green-100 rounded-2xl flex items-center justify-center">
                                            <i class="fas fa-users text-5xl text-emerald-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Tidak ada data user</p>
                                        @if (request('search') || request('role'))
                                            <a href="{{ route('users.index') }}"
                                                class="text-emerald-600 hover:text-emerald-700 font-medium">
                                                <i class="fas fa-redo mr-1"></i>Reset Filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Desktop Pagination -->
            @if ($users->hasPages())
                <div class="px-4 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ===================== RESET PASSWORD MODAL ===================== -->
    <div id="resetPasswordModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-t-2xl"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="bg-orange-100 p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-key text-xl text-orange-500"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg font-bold text-gray-800">Reset Password</h3>
                        <p class="text-xs text-gray-500">
                            User: <span id="resetUserName" class="font-semibold text-gray-700"></span>
                        </p>
                    </div>
                    <button onclick="closeResetPasswordModal()"
                        class="text-gray-400 hover:text-gray-600 transition p-1 flex-shrink-0">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Password Baru -->
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-emerald-500"></i>Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="reset_new_password"
                                class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                                placeholder="Minimal 6 karakter" autocomplete="new-password">
                            <button type="button" onclick="toggleResetPassword('reset_new_password', 'icon_pass1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                <i class="fas fa-eye text-sm" id="icon_pass1"></i>
                            </button>
                        </div>
                        <p id="err_new_password" class="hidden text-red-500 text-xs mt-1.5">
                            <i class="fas fa-exclamation-circle mr-1"></i><span></span>
                        </p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-emerald-500"></i>Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input type="password" id="reset_new_password_confirmation"
                                class="w-full px-4 py-2.5 pr-11 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                                placeholder="Ulangi password baru" autocomplete="new-password">
                            <button type="button"
                                onclick="toggleResetPassword('reset_new_password_confirmation', 'icon_pass2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                                <i class="fas fa-eye text-sm" id="icon_pass2"></i>
                            </button>
                        </div>
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

                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button id="btnResetSubmit" onclick="submitResetPassword()"
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-4 py-2.5 rounded-xl font-semibold transition transform hover:scale-[1.02] active:scale-[0.98] text-sm shadow-md">
                        <i class="fas fa-check mr-2" id="resetBtnIcon"></i>
                        <span id="resetBtnText">Reset Password</span>
                    </button>
                    <button type="button" onclick="closeResetPasswordModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-semibold transition transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== DELETE MODAL ===================== -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="h-1.5 bg-gradient-to-r from-red-500 to-pink-500 rounded-t-2xl"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-red-100 p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-trash-alt text-xl text-red-500"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg font-bold text-gray-800">Konfirmasi Hapus</h3>
                        <p class="text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-5">
                    <p class="text-sm text-gray-700">
                        Anda akan menghapus user: <span id="deleteUserName" class="font-bold text-red-600"></span>
                    </p>
                </div>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-4 py-2.5 rounded-xl font-semibold transition transform hover:scale-[1.02] active:scale-[0.98] text-sm shadow-md">
                            <i class="fas fa-trash mr-2"></i>Ya, Hapus
                        </button>
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-semibold transition transform hover:scale-[1.02] active:scale-[0.98] text-sm">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let _resetUserId = null;

            // ================================================================
            // FLASH MESSAGE TOAST
            // ================================================================
            function _showFlashMessage(message, type = 'success') {
                const isSuccess = type === 'success';
                const container = document.createElement('div');
                container.style.opacity = '0';
                container.style.transition = 'opacity 0.3s ease';
                container.className = `fixed top-5 right-5 z-[100] flex items-center gap-3 px-5 py-4 rounded-xl shadow-xl text-sm font-semibold ${
                    isSuccess ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'
                }`;
                container.innerHTML = `
                    <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(container);
                setTimeout(() => container.style.opacity = '1', 10);
                setTimeout(() => {
                    container.style.opacity = '0';
                    setTimeout(() => container.remove(), 300);
                }, 3500);
            }

            // ================================================================
            // RESET PASSWORD MODAL
            // ================================================================
            function openResetPasswordModal(userId, userName) {
                _resetUserId = userId;
                document.getElementById('resetUserName').textContent = userName;
                _clearResetForm();
                document.getElementById('resetPasswordModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => document.getElementById('reset_new_password').focus(), 100);
            }

            function closeResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
                _resetUserId = null;
                _clearResetForm();
            }

            function _clearResetForm() {
                document.getElementById('reset_new_password').value = '';
                document.getElementById('reset_new_password_confirmation').value = '';
                _resetFieldType('reset_new_password', 'icon_pass1');
                _resetFieldType('reset_new_password_confirmation', 'icon_pass2');
                _hideError('err_new_password');
                _hideError('err_confirm_password');
                _hideGeneralError();
                document.getElementById('reset_new_password').classList.remove('border-red-400');
                document.getElementById('reset_new_password_confirmation').classList.remove('border-red-400');
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

            function _validateResetForm(password, confirmation) {
                let valid = true;
                document.getElementById('reset_new_password').classList.remove('border-red-400');
                document.getElementById('reset_new_password_confirmation').classList.remove('border-red-400');
                _hideError('err_new_password');
                _hideError('err_confirm_password');
                _hideGeneralError();

                if (!password || password.length < 6) {
                    _showError('err_new_password', 'Password minimal 6 karakter');
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
                if (!_resetUserId) return;

                const password = document.getElementById('reset_new_password').value;
                const confirmation = document.getElementById('reset_new_password_confirmation').value;

                if (!_validateResetForm(password, confirmation)) return;

                _setResetBtnLoading(true);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        _showGeneralError('CSRF token tidak ditemukan. Silakan refresh halaman.');
                        _setResetBtnLoading(false);
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
            function _showError(elId, message) {
                const el = document.getElementById(elId);
                if (!el) return;
                el.querySelector('span').textContent = message;
                el.classList.remove('hidden');
            }

            function _hideError(elId) {
                const el = document.getElementById(elId);
                if (el) el.classList.add('hidden');
            }

            function _showGeneralError(message) {
                const el = document.getElementById('resetGeneralError');
                const txt = document.getElementById('resetGeneralErrorText');
                if (el && txt) {
                    txt.textContent = message;
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
            function confirmDelete(userId, userName) {
                document.getElementById('deleteUserName').textContent = userName;
                document.getElementById('deleteForm').action = `/users/${userId}`;
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
            document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
                if (e.target === this) closeResetPasswordModal();
            });

            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) closeDeleteModal();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeResetPasswordModal();
                    closeDeleteModal();
                }
            });

            document.getElementById('resetPasswordModal').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitResetPassword();
                }
            });
        </script>
    @endpush
@endsection
