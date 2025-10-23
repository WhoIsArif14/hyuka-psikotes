<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivationCode; // Penting: Menggunakan model ActivationCode
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomLoginController extends Controller
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
     * Alur: Validasi Kode di activation_codes -> Buat/Login User di tabel users -> Redirect ke edit data diri.
     */
    public function login(Request $request)
    {
        // Ganti nama field validasi ke 'activation_code'
        $request->validate([
            'activation_code' => 'required|string', 
        ], [
            'activation_code.required' => 'Kode Aktivasi wajib diisi.',
        ]);
        
        // Bersihkan tanda hubung ('-') dan kapitalisasi input
        $inputCode = strtoupper(str_replace('-', '', $request->activation_code));

        // 1. CARI KODE AKTIVASI di tabel activation_codes
        $activationCode = ActivationCode::where('code', $inputCode)->first();

        if (!$activationCode) {
            throw ValidationException::withMessages([
                'activation_code' => ['Kode Aktivasi tidak valid atau tidak ditemukan.'],
            ]);
        }
        
        $user = null;

        // 2. TENTUKAN STATUS KODE: Sudah terpakai atau belum?
        if ($activationCode->user_id) {
            // --- KODE SUDAH PERNAH DIPAKAI (LOGIN USER LAMA) ---
            
            // Cek kadaluarsa (opsional, tergantung kebijakan Anda)
            if ($activationCode->expires_at && $activationCode->expires_at->isPast()) {
                 throw ValidationException::withMessages([
                    'activation_code' => ['Kode sudah kadaluarsa.'],
                ]);
            }
            
            $user = User::find($activationCode->user_id);
            if (!$user) {
                 // Jika user sudah terhapus tapi kode masih terikat
                 throw ValidationException::withMessages([
                    'activation_code' => ['Data user terkait kode tidak ditemukan. Silakan hubungi admin.'],
                ]);
            }

        } else {
            // --- KODE BELUM PERNAH DIPAKAI (BUAT USER BARU) ---

            // Cek kadaluarsa sebelum membuat user baru
            if ($activationCode->expires_at && $activationCode->expires_at->isPast()) {
                 throw ValidationException::withMessages([
                    'activation_code' => ['Kode sudah kadaluarsa.'],
                ]);
            }
            
            // 3. Buat User baru di tabel 'users'
            $user = User::create([
                'name' => 'Peserta ' . $inputCode,
                'email' => $inputCode . '@temp.hyuka.com', 
                'password' => Hash::make(Str::random(10)), 
                'role' => 'user',
                'kode_aktivasi_peserta' => $inputCode, // Disimpan untuk referensi, bukan untuk validasi login
            ]);

            // 4. Kaitkan Kode Aktivasi dengan User yang baru dibuat
            $activationCode->user_id = $user->id;
            $activationCode->save();
        }
        
        // 5. Login User
        Auth::login($user); 
        $request->session()->regenerate();
        
        // Pengalihan ke halaman pengisian data diri (UserDataController@edit)
        return redirect()->route('user.data.edit'); 
    }

    /**
     * Melakukan logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Anda telah keluar. Terima kasih!');
    }
}
