<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivationCode;
use App\Models\Jenjang;

class UserDataController extends Controller
{
    /**
     * Menampilkan form untuk mengisi atau mengedit data diri peserta.
     */
    public function edit()
    {
        $user = Auth::user();
        $jenjangs = Jenjang::orderBy('name')->get();

        // Get the current test module from activation code
        $activationCodeId = session('activation_code_id');
        $currentModule = null;

        if ($activationCodeId) {
            $activationCode = ActivationCode::with('test')->find($activationCodeId);
            if ($activationCode && $activationCode->test) {
                $currentModule = $activationCode->test;
            }
        }

        return view('user.edit', compact('user', 'jenjangs', 'currentModule'));
    }

    /**
     * Menyimpan data diri yang diisi peserta DAN MENGINISIALISASI SESI TES.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi dan Update data user
        // Email tidak perlu unique karena satu orang bisa mengikuti beberapa test dengan kode aktivasi berbeda
        // Phone number: hanya angka, maksimal 13 digit
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => ['nullable', 'string', 'max:13', 'regex:/^[0-9]*$/'],
            'education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
        ], [
            'phone_number.max' => 'Nomor telepon maksimal 13 digit.',
            'phone_number.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        $user->update($validatedData);

        // ***************************************************************
        // !!! PERBAIKAN KRITIS !!!
        // Menginisialisasi Sesi Tes untuk mengatasi error "Tidak ada sesi tes aktif"
        // ***************************************************************

        // Ambil activation code ID dari session yang disimpan saat login
        $activationCodeId = session('activation_code_id');

        if (!$activationCodeId) {
            // Fallback: cari berdasarkan user_id (untuk backward compatibility)
            $activationCode = ActivationCode::where('user_id', $user->id)
                ->whereIn('status', ['Used', 'Pending'])
                ->first();
        } else {
            $activationCode = ActivationCode::find($activationCodeId);
        }

        if ($activationCode && $activationCode->test_id) {

            // KUNCI PERBAIKAN: Menyimpan ID Tes ke variabel Session yang digunakan oleh route tes.
            session([
                'active_test_id' => $activationCode->test_id,
            ]);

            // Status tetap "Used" - user akan mengerjakan test
            // Status "Completed" hanya diset setelah SEMUA test selesai dikerjakan

            // Hapus activation code ID dari session setelah digunakan
            $request->session()->forget('activation_code_id');

            // Arahkan ke halaman start tes
            return redirect()->route('tests.start')
                ->with('success', 'Data diri berhasil disimpan. Tes dimulai!');
        }
        
        // Fallback jika kode aktivasi tidak ditemukan atau tidak memiliki ID Tes
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke '/' bukan 'login' route untuk menghindari potential loop
        return redirect('/')->with('error', 'Gagal menemukan sesi tes yang aktif. Silakan masukkan kode aktivasi lagi.');
    }
}