<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Copy file ke public_html/storage
     */
    private function copyToPublicStorage($filename)
    {
        $sourcePath = storage_path('app/public/users/' . $filename);
        $destPath = '/home/irryvkri/public_html/storage/users/' . $filename;

        Log::info("=== Copy To Public Storage ===");
        Log::info("Source: " . $sourcePath);
        Log::info("Dest: " . $destPath);

        if (!File::exists('/home/irryvkri/public_html/storage/users')) {
            File::makeDirectory('/home/irryvkri/public_html/storage/users', 0755, true);
        }

        if (File::exists($sourcePath)) {
            try {
                File::copy($sourcePath, $destPath);
                Log::info("✅ Successfully copied: " . $filename);
            } catch (\Exception $e) {
                Log::error("❌ Failed to copy: " . $e->getMessage());
            }
        } else {
            Log::error("❌ Source file not found: " . $sourcePath);
        }
    }

    /**
     * Hapus file dari kedua lokasi
     */
    private function deleteFromBothLocations($filename)
    {
        Log::info("=== Delete From Both Locations: " . $filename . " ===");

        Storage::delete('public/users/' . $filename);

        $publicPath = '/home/irryvkri/public_html/storage/users/' . $filename;
        if (File::exists($publicPath)) {
            File::delete($publicPath);
            Log::info("✅ Deleted from public_html: " . $filename);
        }
    }

    /**
     * Upload file gambar ke storage
     */
    private function uploadFile($file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = storage_path('app/public/users');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $this->copyToPublicStorage($filename);

        Log::info("✅ File uploaded: " . $filename);
        return $filename;
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        $query = DB::table('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_user', 'like', "%{$search}%")
                    ->orWhere('username_user', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_user', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('users.index', compact('users'));
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        return view('users.create');
    }

    // =========================================================
    // STORE
    // =========================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user'     => 'required|string|max:150',
            'username_user' => 'required|string|max:50|unique:user,username_user',
            'password_user' => 'required|string|min:6|confirmed',
            'role_user'     => ['required', Rule::in(['owner', 'kasir'])],
            'gambar_user'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ], [
            'nama_user.required'      => 'Nama harus diisi',
            'username_user.required'  => 'Username harus diisi',
            'username_user.unique'    => 'Username sudah digunakan',
            'password_user.required'  => 'Password harus diisi',
            'password_user.min'       => 'Password minimal 6 karakter',
            'password_user.confirmed' => 'Konfirmasi password tidak cocok',
            'role_user.required'      => 'Role harus dipilih',
            'gambar_user.image'       => 'File harus berupa gambar',
            'gambar_user.mimes'       => 'Format gambar harus JPG, PNG, atau GIF',
            'gambar_user.max'         => 'Ukuran gambar maksimal 2MB',
        ]);

        $data = [
            'nama_user'     => $validated['nama_user'],
            'username_user' => $validated['username_user'],
            'password_user' => bcrypt($validated['password_user']),
            'role_user'     => $validated['role_user'],
            'created_at'    => now(),
        ];

        if ($request->hasFile('gambar_user')) {
            $data['gambar_user'] = $this->uploadFile($request->file('gambar_user'));
        }

        DB::table('user')->insert($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show($id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();

        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        $stats = [
            'total_kasir'     => DB::table('kasir')->where('id_user', $id)->count(),
            'kasir_aktif'     => DB::table('kasir')->where('id_user', $id)->whereNull('waktu_close')->count(),
            'total_transaksi' => DB::table('penjualan')
                ->join('kasir', 'penjualan.id_kasir', '=', 'kasir.id_kasir')
                ->where('kasir.id_user', $id)
                ->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit($id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();

        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        if (Auth::user()->id_user == $user->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Gunakan menu profil untuk edit akun Anda sendiri');
        }

        return view('users.edit', compact('user'));
    }

    // =========================================================
    // UPDATE
    // =========================================================
    public function update(Request $request, $id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();

        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        if (Auth::user()->id_user == $user->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat mengedit akun sendiri melalui halaman ini');
        }

        $validated = $request->validate([
            'nama_user'     => 'required|string|max:150',
            'username_user' => [
                'required',
                'string',
                'max:50',
                Rule::unique('user', 'username_user')->ignore($id, 'id_user')
            ],
            'role_user'     => ['required', Rule::in(['owner', 'kasir'])],
            'password_user' => 'nullable|string|min:6|confirmed',
            'gambar_user'   => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'hapus_gambar'  => 'nullable|boolean',
        ], [
            'nama_user.required'      => 'Nama harus diisi',
            'username_user.required'  => 'Username harus diisi',
            'username_user.unique'    => 'Username sudah digunakan',
            'password_user.min'       => 'Password minimal 6 karakter',
            'password_user.confirmed' => 'Konfirmasi password tidak cocok',
            'role_user.required'      => 'Role harus dipilih',
            'gambar_user.image'       => 'File harus berupa gambar',
            'gambar_user.mimes'       => 'Format gambar harus JPG, PNG, atau GIF',
            'gambar_user.max'         => 'Ukuran gambar maksimal 2MB',
        ]);

        $updateData = [
            'nama_user'     => $validated['nama_user'],
            'username_user' => $validated['username_user'],
            'role_user'     => $validated['role_user'],
        ];

        if ($request->filled('password_user')) {
            $updateData['password_user'] = bcrypt($validated['password_user']);
        }

        if ($request->input('hapus_gambar') == '1') {
            if ($user->gambar_user) {
                $this->deleteFromBothLocations($user->gambar_user);
            }
            $updateData['gambar_user'] = null;
        } elseif ($request->hasFile('gambar_user')) {
            if ($user->gambar_user) {
                $this->deleteFromBothLocations($user->gambar_user);
            }
            $updateData['gambar_user'] = $this->uploadFile($request->file('gambar_user'));
        }

        DB::table('user')->where('id_user', $id)->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    // =========================================================
    // DESTROY
    // =========================================================
    public function destroy($id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();

        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        if (Auth::user()->id_user == $user->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $hasActiveKasir = DB::table('kasir')
            ->where('id_user', $id)
            ->whereNull('waktu_close')
            ->exists();

        if ($hasActiveKasir) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus user yang memiliki kasir aktif');
        }

        $hasTransactions = DB::table('kasir')->where('id_user', $id)->exists();

        if ($hasTransactions) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus user yang memiliki riwayat transaksi.');
        }

        if ($user->gambar_user) {
            $this->deleteFromBothLocations($user->gambar_user);
        }

        DB::table('user')->where('id_user', $id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    // =========================================================
    // RESET PASSWORD ✅ RETURN JSON — sinkron dengan AJAX di
    // index.blade.php dan show.blade.php
    // =========================================================
    public function resetPassword(Request $request, $id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        try {
            $request->validate([
                'new_password' => 'required|string|min:6|confirmed',
            ], [
                'new_password.required'  => 'Password baru harus diisi',
                'new_password.min'       => 'Password minimal 6 karakter',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        }

        try {
            DB::table('user')->where('id_user', $id)->update([
                'password_user' => bcrypt($request->new_password),
            ]);

            Log::info("✅ Password reset for user ID: " . $id);

            return response()->json([
                'success' => true,
                'message' => 'Password user berhasil direset',
            ], 200);
        } catch (\Exception $e) {
            Log::error("❌ Reset password error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mereset password. Silakan coba lagi.',
            ], 500);
        }
    }

    // =========================================================
    // PROFILE
    // =========================================================
    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    // =========================================================
    // UPDATE PROFILE
    // =========================================================
    public function updateProfile(Request $request)
    {
        try {
            $currentUser = Auth::user();

            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah berakhir, silakan login kembali',
                ], 401);
            }

            $validated = $request->validate([
                'nama_user'        => 'required|string|max:150',
                'username_user'    => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('user', 'username_user')->ignore($currentUser->id_user, 'id_user')
                ],
                'current_password' => 'required_with:new_password',
                'new_password'     => 'nullable|string|min:6|confirmed',
                'gambar_user'      => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'hapus_gambar'     => 'nullable|boolean',
            ], [
                'nama_user.required'             => 'Nama harus diisi',
                'nama_user.max'                  => 'Nama maksimal 150 karakter',
                'username_user.required'         => 'Username harus diisi',
                'username_user.unique'           => 'Username sudah digunakan',
                'username_user.max'              => 'Username maksimal 50 karakter',
                'current_password.required_with' => 'Password lama harus diisi untuk mengganti password',
                'new_password.min'               => 'Password baru minimal 6 karakter',
                'new_password.confirmed'         => 'Konfirmasi password tidak cocok',
                'gambar_user.image'              => 'File harus berupa gambar',
                'gambar_user.mimes'              => 'Format gambar harus JPG, PNG, atau GIF',
                'gambar_user.max'                => 'Ukuran gambar maksimal 2MB',
            ]);

            $updateData = [
                'nama_user'     => $validated['nama_user'],
                'username_user' => $validated['username_user'],
            ];

            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $currentUser->password_user)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password lama tidak sesuai',
                        'errors'  => ['current_password' => ['Password lama tidak sesuai']],
                    ], 422);
                }
                $updateData['password_user'] = bcrypt($validated['new_password']);
            }

            if ($request->input('hapus_gambar') == '1') {
                if ($currentUser->gambar_user) {
                    $this->deleteFromBothLocations($currentUser->gambar_user);
                }
                $updateData['gambar_user'] = null;
            } elseif ($request->hasFile('gambar_user')) {
                if ($currentUser->gambar_user) {
                    $this->deleteFromBothLocations($currentUser->gambar_user);
                }
                $updateData['gambar_user'] = $this->uploadFile($request->file('gambar_user'));
            }

            DB::table('user')->where('id_user', $currentUser->id_user)->update($updateData);

            Log::info("✅ Profile updated for user ID: " . $currentUser->id_user);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
