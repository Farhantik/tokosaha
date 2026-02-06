@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('users.index') }}"
                    class="bg-white hover:bg-gray-100 text-gray-800 p-3 rounded-xl shadow-lg transition hover:scale-105">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        <i class="fas fa-user-circle mr-2 text-blue-600"></i>Detail User
                    </h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap pengguna</p>
                </div>
            </div>

            @if (Auth::user()->id_user != $user->id_user)
                <div class="flex gap-2">
                    <a href="{{ route('users.edit', $user->id_user) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl font-semibold transition hover:scale-105 shadow-lg">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            @endif
        </div>

        <!-- User Info Card -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                <!-- Avatar -->
                @if ($user->gambar_user && file_exists(public_path('uploads/users/' . $user->gambar_user)))
                    <img src="{{ asset('uploads/users/' . $user->gambar_user) }}" alt="{{ $user->nama_user }}"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white/30 shadow-2xl">
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

                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
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

                        @if (Auth::user()->id_user == $user->id_user)
                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-user-circle mr-2"></i>Akun Anda
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex gap-4 text-center">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-3xl font-bold">{{ $stats['total_kasir'] }}</p>
                        <p class="text-sm text-white/80">Sesi Kasir</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-3xl font-bold">{{ $stats['total_transaksi'] }}</p>
                        <p class="text-sm text-white/80">Transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kasir Aktif -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transition hover:shadow-xl hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-green-100 p-4 rounded-xl">
                        <i class="fas fa-cash-register text-3xl text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Kasir Aktif</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['kasir_aktif'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Sesi -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transition hover:shadow-xl hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 p-4 rounded-xl">
                        <i class="fas fa-history text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Sesi</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_kasir'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transition hover:shadow-xl hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-purple-100 p-4 rounded-xl">
                        <i class="fas fa-shopping-cart text-3xl text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Transaksi</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_transaksi'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Information -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-info-circle mr-2"></i>Informasi Detail
                </h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- ID User -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-gray-100 p-3 rounded-lg">
                            <i class="fas fa-hashtag text-gray-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">ID User</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $user->id_user }}</p>
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Nama Lengkap</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $user->nama_user }}</p>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-at text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Username</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $user->username_user }}</p>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-user-tag text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Role</p>
                            <p class="text-lg font-semibold text-gray-800 capitalize">{{ $user->role_user }}</p>
                        </div>
                    </div>

                    <!-- Terdaftar -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <i class="fas fa-calendar-alt text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Terdaftar Sejak</p>
                            <p class="text-lg font-semibold text-gray-800">
                                {{ date('d F Y, H:i', strtotime($user->created_at)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Status Kasir -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-orange-100 p-3 rounded-lg">
                            <i class="fas fa-info-circle text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Status</p>
                            @if ($stats['kasir_aktif'] > 0)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Kasir Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-minus-circle mr-1"></i>Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if (Auth::user()->id_user != $user->id_user)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-cog mr-2"></i>Aksi
                </h3>
                <div class="flex flex-wrap gap-3">
                    <button onclick="openResetPasswordModal()"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition hover:scale-105 shadow-lg">
                        <i class="fas fa-key mr-2"></i>Reset Password
                    </button>
                    <button onclick="confirmDelete()"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition hover:scale-105 shadow-lg">
                        <i class="fas fa-trash mr-2"></i>Hapus User
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-orange-100 p-3 rounded-xl">
                        <i class="fas fa-key text-2xl text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Reset Password</h3>
                        <p class="text-sm text-gray-600">User: <span class="font-semibold">{{ $user->nama_user }}</span>
                        </p>
                    </div>
                </div>

                <form action="{{ route('users.reset-password', $user->id_user) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="reset_new_password" name="new_password" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="Minimal 6 karakter">
                            <button type="button" onclick="togglePassword('reset_new_password')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition">
                                <i class="fas fa-eye" id="reset_new_password-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input type="password" id="reset_new_password_confirmation" name="new_password_confirmation"
                                required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="Ulangi password baru">
                            <button type="button" onclick="togglePassword('reset_new_password_confirmation')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition">
                                <i class="fas fa-eye" id="reset_new_password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition hover:scale-105">
                            <i class="fas fa-check mr-2"></i>Reset Password
                        </button>
                        <button type="button" onclick="closeResetPasswordModal()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition hover:scale-105">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-red-100 p-3 rounded-xl">
                        <i class="fas fa-trash-alt text-2xl text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                        <p class="text-sm text-gray-600">Apakah Anda yakin?</p>
                    </div>
                </div>

                <p class="text-gray-700 mb-6">
                    Anda akan menghapus user: <strong class="text-red-600">{{ $user->nama_user }}</strong>
                    <br><span class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan!</span>
                </p>
                <form action="{{ route('users.destroy', $user->id_user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-6 py-3 rounded-xl font-semibold transition hover:scale-105">
                            <i class="fas fa-check mr-2"></i>Ya, Hapus
                        </button>
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition hover:scale-105">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.remove('hidden');
            }

            function closeResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.add('hidden');
            }

            function confirmDelete() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Toggle password visibility
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

            // Close modals with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeResetPasswordModal();
                    closeDeleteModal();
                }
            });

            // Close modal when clicking outside
            document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
                if (e.target === this) closeResetPasswordModal();
            });

            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) closeDeleteModal();
            });
        </script>
    @endpush
@endsection
