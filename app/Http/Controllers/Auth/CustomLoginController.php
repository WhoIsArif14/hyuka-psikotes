<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CustomLoginController extends Controller
{
    /**
     * Menampilkan halaman login awal (memasukkan kode).
     * NAMA METHOD DIPERBAIKI AGAR SESUAI DENGAN ROUTE
     */
    public function showCodeForm()
    {
        return view('auth.login');
    }

    /**
     * Langkah 1: Memvalidasi kode aktivasi yang dimasukkan.
     */
    public function processCode(Request $request)
    {
        $request->validate(['login_code' => 'required|string']);
        $code = strtoupper($request->login_code);

        $activationCode = ActivationCode::where('code', $code)
            ->whereNull('user_id') // Cek apakah kode belum pernah dipakai
            ->where('expires_at', '>', now()) // Cek apakah kode belum kadaluarsa
            ->first();

        // Jika kode tidak valid, kembali dengan pesan error
        if (!$activationCode) {
            return redirect()->back()->with('error', 'Kode aktivasi tidak valid, sudah digunakan, atau telah kadaluarsa.');
        }

        // Jika valid, simpan kode di session dan arahkan ke halaman pendaftaran
        Session::put('valid_activation_code', $code);
        return redirect()->route('code.register.show');
    }

    /**
     * Langkah 2: Menampilkan halaman untuk mengisi data diri.
     */
    public function showRegisterWithCodeForm()
    {
        // Pastikan pengguna sudah melewati langkah 1
        if (!Session::has('valid_activation_code')) {
            return redirect()->route('login');
        }

        return view('auth.register-with-code');
    }

    /**
     * Langkah 2: Mendaftarkan peserta, mengaitkan kode, dan login.
     */
    public function registerAndLogin(Request $request)
    {
        // Pastikan pengguna sudah melewati langkah 1
        $code = Session::get('valid_activation_code');
        if (!$code) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);

        $activationCode = ActivationCode::where('code', $code)->whereNull('user_id')->first();

        // Keamanan ganda, cek lagi jika kode tiba-tiba dipakai orang lain
        if (!$activationCode) {
            return redirect()->route('login')->with('error', 'Kode ini baru saja digunakan. Silakan minta kode baru.');
        }

        // Buat atau temukan user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(\Illuminate\Support\Str::random(10)), // Buat password acak
            'role' => 'user',
        ]);

        // Kaitkan kode dengan user yang baru dibuat
        $activationCode->user_id = $user->id;
        $activationCode->save();

        // Login-kan user secara manual
        Auth::login($user);
        $request->session()->regenerate();
        Session::forget('valid_activation_code'); // Hapus kode dari session

        // Simpan ID kode aktivasi di session untuk ditandai 'selesai' setelah tes
        Session::put('active_code_id', $activationCode->id);
        
        // Arahkan langsung ke halaman pengerjaan tes
        return redirect()->route('tests.show', $activationCode->test_id);
    }

    /**
     * Melakukan logout untuk peserta setelah tes selesai.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Anda telah menyelesaikan tes. Terima kasih!');
    }
}

