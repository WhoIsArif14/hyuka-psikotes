<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TestAccessController extends Controller
{
    /**
     * Menampilkan halaman login (form Kode Aktivasi Peserta).
     */
    public function showLoginForm(Request $request)
    {
        // ✅ TAMBAHAN: Jika sudah login, redirect sesuai status
        if (Auth::check()) {
            $user = Auth::user();

            // Jika admin, redirect ke admin dashboard
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // ✅ KUNCI: Cek apakah ada active_test_id di session
            if (session('active_test_id')) {
                // Jika sudah ada test aktif, langsung ke tests.start
                return redirect()->route('tests.start');
            }

            // ✅ CRITICAL FIX: Jika user sudah login tapi TIDAK ada active_test_id,
            // artinya session test sudah habis/tidak valid. Logout dan minta login ulang.
            // Ini mencegah loop antara showLoginForm -> tests.start -> login

            // Jika user belum isi data diri DAN ada activation session, redirect ke form
            if ((empty($user->name) || $user->name === 'Peserta ' . $user->kode_aktivasi_peserta)
                && session('current_activation_id')) {
                return redirect()->route('user.data.edit');
            }

            // ✅ DEFAULT: Jika sudah login tapi tidak ada active_test_id, logout
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Kembalikan view login langsung tanpa redirect untuk menghindari loop
            return view('auth.login')->with('info', 'Sesi tes Anda telah berakhir. Silakan masukkan kode aktivasi untuk memulai tes baru.');
        }

        return view('auth.login');
    }

    /**
     * Melakukan login peserta menggunakan Kode Aktivasi.
     * Alur: Validasi Kode -> Cek User -> Buat/Login User -> Arahkan ke Data Diri.
     */
    public function login(Request $request)
    {
        // ✅ VALIDASI INPUT
        $request->validate([
            'kode_aktivasi_peserta' => 'required|string', 
        ], [
            'kode_aktivasi_peserta.required' => 'Kode Aktivasi Peserta wajib diisi.',
        ]);
        
        try {
            // Bersihkan dan Kapitalisasi input kode
            $inputCode = strtoupper(str_replace('-', '', $request->kode_aktivasi_peserta));

            // 1. Cari Kode Aktivasi di tabel activation_codes
            $activationCode = ActivationCode::whereRaw("REPLACE(code, '-', '') = ?", [str_replace('-', '', $inputCode)])
                ->first();

            // Jika kode tidak ditemukan sama sekali
            if (!$activationCode) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'kode_aktivasi_peserta' => 'Kode Aktivasi tidak valid atau tidak ditemukan.'
                    ]);
            }
            
            // 2. Cek Kadaluarsa (Berlaku untuk kode lama maupun baru)
            if ($activationCode->expires_at && $activationCode->expires_at->isPast()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'kode_aktivasi_peserta' => 'Kode sudah kadaluarsa.'
                    ]);
            }

            // ✅ VALIDASI: Kode harus SEKALI PAKAI
            // Jika kode sudah punya user_id atau status bukan Pending, TOLAK
            if ($activationCode->user_id || $activationCode->status !== 'Pending') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'kode_aktivasi_peserta' => 'Kode aktivasi sudah digunakan dan tidak bisa dipakai lagi. Silakan hubungi admin untuk kode baru.'
                    ]);
            }

            // --- KODE BARU (BUAT USER BARU) ---

            // 3. Buat User baru
            $user = User::create([
                'name' => 'Peserta ' . $inputCode,
                'email' => $inputCode . '@temp.hyuka.com',
                'password' => Hash::make(Str::random(10)),
                'role' => 'user',
                'kode_aktivasi_peserta' => $inputCode,
            ]);

            // 4. Kaitkan Kode Aktivasi dengan User yang baru dibuat DAN SET STATUS + USED_AT
            $activationCode->user_id = $user->id;
            $activationCode->status = 'Used';
            $activationCode->used_at = now();
            $activationCode->save();

            Log::info('New user created and logged in', [
                'user_id' => $user->id,
                'code' => $inputCode,
                'status' => 'Used'
            ]);
            
            // 5. Login User dengan "remember me" untuk persistence
            Auth::login($user, true); // ✅ TAMBAHKAN true untuk remember
            
            // ✅ PENTING: Regenerate session SETELAH login
            $request->session()->regenerate();
            
            // ✅ SIMPAN data penting ke session
            session([
                'current_activation_id' => $activationCode->id,
                'activation_code_id' => $activationCode->id, // Untuk UserDataController
                'activation_code' => $inputCode,
                'login_time' => now()->timestamp,
            ]);

            // 6. Pengalihan ke halaman pengisian data diri
            return redirect()
                ->route('user.data.edit')
                ->with('success', 'Login berhasil! Silakan lengkapi data diri Anda.');

        } catch (\Exception $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $request->kode_aktivasi_peserta
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'kode_aktivasi_peserta' => 'Terjadi kesalahan saat login. Silakan coba lagi.'
                ]);
        }
    }

    /**
     * Logout peserta.
     */
    public function logout(Request $request)
    {
        $userName = Auth::user()->name ?? 'Peserta';
        
        // ✅ LOGOUT dengan proper cleanup
        Auth::guard('web')->logout();

        // ✅ INVALIDATE dan REGENERATE session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', [
            'user' => $userName,
            'ip' => $request->ip()
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Anda telah berhasil keluar. Terima kasih!');
    }
}