<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Menyimpan data diri yang diisi peserta.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi data diri peserta
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            // Tambahkan kolom lain sesuai kebutuhan tabel users kamu
        ]);

        // Update data user yang sedang login
        $user->update($validatedData);

        // Arahkan ke halaman start tes (ubah sesuai route kamu)
        return redirect()->route('tests.start')
            ->with('status', 'Data diri berhasil disimpan. Anda dapat memulai tes.');
    }
}
