<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestAccessController extends Controller
{
    /**
     * Menampilkan halaman login (form Kode Aktivasi Peserta).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Melakukan login peserta menggunakan Kode Aktivasi.
     * Alur: Validasi Kode -> Cek User -> Buat/Login User -> Arahkan ke Data Diri.
     */
    public function login(Request $request)
    {
        $request->validate([
            'kode_aktivasi_peserta' => 'required|string', 
        ], [
            'kode_aktivasi_peserta.required' => 'Kode Aktivasi Peserta wajib diisi.',
        ]);
        
        // Bersihkan dan Kapitalisasi input kode
        $inputCode = strtoupper(str_replace('-', '', $request->kode_aktivasi_peserta));

        // 1. Cari Kode Aktivasi di tabel activation_codes
        $activationCode = ActivationCode::whereRaw("REPLACE(code, '-', '') = ?", [str_replace('-', '', $inputCode)])->first();

        // Jika kode tidak ditemukan sama sekali
        if (!$activationCode) {
            throw ValidationException::withMessages([
                'kode_aktivasi_peserta' => ['Kode Aktivasi tidak valid atau tidak ditemukan.'],
            ]);
        }
        
        // 2. Cek Kadaluarsa (Berlaku untuk kode lama maupun baru)
        if ($activationCode->expires_at && $activationCode->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'kode_aktivasi_peserta' => ['Kode sudah kadaluarsa.'],
            ]);
        }

        if ($activationCode->user_id) {
            // --- KODE SUDAH PERNAH DIPAKAI (LOGIN USER LAMA) ---
            $user = User::find($activationCode->user_id);
            
            // Cek apakah user ditemukan
            if (!$user) {
                // Jika user ID ada di kode aktivasi tapi user-nya hilang
                throw ValidationException::withMessages(['kode_aktivasi_peserta' => ['User yang terhubung ke kode ini tidak ditemukan.']]);
            }

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
        }
        
        // 5. Login User
        Auth::login($user); 
        $request->session()->regenerate();
        
        // ***************************************************************
        // !!! PERBAIKAN KRITIS !!!
        // Simpan ID Kode Aktivasi ke session untuk digunakan di Controller berikutnya.
        // ***************************************************************
        session(['current_activation_id' => $activationCode->id]);
        
        // Pengalihan ke halaman pengisian data diri (di mana user akan mengisi data asli)
        return redirect()->route('user.data.edit'); 
    }

    /**
     * Logout peserta.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar.');
    }
}