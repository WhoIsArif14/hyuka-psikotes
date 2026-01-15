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
        
        // Normalisasi input: kapitalisasi dan format dengan dash jika perlu
        $inputCode = strtoupper(trim($request->activation_code));

        // Jika user input tanpa dash, tambahkan dash di posisi yang benar (XXXX-XXXX)
        $inputCodeNoDash = str_replace('-', '', $inputCode);
        if (strlen($inputCodeNoDash) === 8 && !str_contains($inputCode, '-')) {
            $inputCode = substr($inputCodeNoDash, 0, 4) . '-' . substr($inputCodeNoDash, 4, 4);
        }

        // 1. CARI KODE AKTIVASI di tabel activation_codes (coba dengan dan tanpa dash)
        $activationCode = ActivationCode::where('code', $inputCode)
            ->orWhere('code', $inputCodeNoDash)
            ->first();

        if (!$activationCode) {
            throw ValidationException::withMessages([
                'activation_code' => ['Kode Aktivasi tidak valid atau tidak ditemukan.'],
            ]);
        }
        
        $user = null;

        // 2. KODE HARUS BENAR-BENAR SEKALI PAKAI
        // Jika kode sudah pernah digunakan (punya user_id atau status bukan Pending), TOLAK
        if ($activationCode->user_id || $activationCode->status !== 'Pending') {
            throw ValidationException::withMessages([
                'activation_code' => ['Kode aktivasi sudah digunakan dan tidak bisa dipakai lagi. Silakan hubungi admin untuk kode baru.'],
            ]);
        }

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
            'kode_aktivasi_peserta' => $inputCode,
        ]);

        // 4. Kaitkan Kode Aktivasi dengan User yang baru dibuat dan set tanggal penggunaan
        $activationCode->user_id = $user->id;
        $activationCode->status = 'Used';
        $activationCode->used_at = now();
        $activationCode->save();
        
        // 5. Login User
        Auth::login($user);
        $request->session()->regenerate();

        // Simpan activation code ID di session untuk digunakan saat update data diri
        $request->session()->put('activation_code_id', $activationCode->id);

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
