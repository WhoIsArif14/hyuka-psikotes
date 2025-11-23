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
    public function showLoginForm()
    {
        // ✅ TAMBAHAN: Jika sudah login, redirect sesuai status
        if (Auth::check()) {
            $user = Auth::user();
            
            // Jika admin, redirect ke admin dashboard
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // Jika user belum isi data diri, redirect ke form
            if (empty($user->name) || $user->name === 'Peserta ' . $user->kode_aktivasi_peserta) {
                return redirect()->route('user.data.edit');
            }
            
            // Jika sudah lengkap, ke halaman tes
            return redirect()->route('tests.start');
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

            if ($activationCode->user_id) {
                // --- KODE SUDAH PERNAH DIPAKAI (LOGIN USER LAMA) ---
                $user = User::find($activationCode->user_id);
                
                // Cek apakah user ditemukan
                if (!$user) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'kode_aktivasi_peserta' => 'User yang terhubung ke kode ini tidak ditemukan.'
                        ]);
                }

                Log::info('Existing user logged in', [
                    'user_id' => $user->id,
                    'code' => $inputCode
                ]);

            } else {
                // --- KODE BELUM PERNAH DIPAKAI (BUAT USER BARU) ---
                
                // 3. Buat User baru
                $user = User::create([
                    'name' => 'Peserta ' . $inputCode,
                    'email' => $inputCode . '@temp.hyuka.com',
                    'password' => Hash::make(Str::random(10)),
                    'role' => 'user',
                    'kode_aktivasi_peserta' => $inputCode, 
                ]);

                // 4. Kaitkan Kode Aktivasi dengan User yang baru dibuat
                $activationCode->user_id = $user->id;
                $activationCode->save();

                Log::info('New user created and logged in', [
                    'user_id' => $user->id,
                    'code' => $inputCode
                ]);
            }
            
            // 5. Login User dengan "remember me" untuk persistence
            Auth::login($user, true); // ✅ TAMBAHKAN true untuk remember
            
            // ✅ PENTING: Regenerate session SETELAH login
            $request->session()->regenerate();
            
            // ✅ SIMPAN data penting ke session
            session([
                'current_activation_id' => $activationCode->id,
                'activation_code' => $inputCode,
                'login_time' => now()->timestamp,
            ]);

            // ✅ TAMBAHAN: Flash message untuk user
            $welcomeMessage = $activationCode->user_id ? 
                'Selamat datang kembali!' : 
                'Login berhasil! Silakan lengkapi data diri Anda.';

            // 6. Pengalihan ke halaman pengisian data diri
            return redirect()
                ->route('user.data.edit')
                ->with('success', $welcomeMessage);

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