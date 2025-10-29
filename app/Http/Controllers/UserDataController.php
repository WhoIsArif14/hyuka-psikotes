<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivationCode; // <<< PASTIKAN MODEL INI DIIMPORT

class UserDataController extends Controller
{
    /**
     * Menampilkan form untuk mengisi atau mengedit data diri peserta.
     */
    public function edit()
    {
        $user = Auth::user();

        // Pastikan file view ini dibuat: resources/views/users/edit.blade.php
        return view('user.edit', compact('user'));
    }

    /**
     * Menyimpan data diri yang diisi peserta DAN MENGINISIALISASI SESI TES.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi dan Update data user
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
        ]);

        $user->update($validatedData);

        // ***************************************************************
        // !!! PERBAIKAN KRITIS !!!
        // Menginisialisasi Sesi Tes untuk mengatasi error "Tidak ada sesi tes aktif"
        // ***************************************************************
        
        $activationId = session('current_activation_id');

        if ($activationId) {
            $activationCode = ActivationCode::find($activationId);

            if ($activationCode && $activationCode->test_id) {
                
                // KUNCI PERBAIKAN: Menyimpan ID Tes ke variabel Session yang digunakan oleh route tes.
                session([
                    'active_test_id' => $activationCode->test_id, 
                ]);
                
                // Hapus session sementara ID aktivasi
                $request->session()->forget('current_activation_id');
                
                // Tandai kode aktivasi sebagai sudah terpakai (opsional, tergantung alur Anda)
                // $activationCode->update(['used_at' => now()]);

                // Arahkan ke halaman start tes
                return redirect()->route('tests.start')
                    ->with('success', 'Data diri berhasil disimpan. Tes dimulai!');
            }
        }
        
        // Fallback jika kode aktivasi tidak ditemukan atau tidak memiliki ID Tes
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('error', 'Gagal menemukan sesi tes yang aktif. Silakan masukkan kode aktivasi lagi.');
    }
}