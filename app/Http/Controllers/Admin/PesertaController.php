<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    /**
     * Menampilkan daftar semua peserta (user dengan role 'user').
     */
    public function index(Request $request)
    {
        // Mulai query dengan filter hanya untuk role 'user'
        $query = User::where('role', 'user');

        // Tambahkan fungsionalitas pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $peserta = $query->latest()->paginate(15)->withQueryString();

        return view('admin.peserta.index', compact('peserta'));
    }

    /**
     * Menampilkan detail dan riwayat tes seorang peserta.
     */
    public function show(User $user)
    {
        // Pastikan kita hanya bisa melihat detail peserta, bukan admin lain.
        if ($user->role !== 'user') {
            abort(404);
        }

        // Ambil riwayat tes milik user ini
        $user->load(['testResults.test']);

        return view('admin.peserta.show', compact('user'));
    }

    /**
     * Menghapus data peserta.
     */
    public function destroy(User $user)
    {
        // Pastikan kita tidak menghapus admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.peserta.index')->with('error', 'Akun admin tidak bisa dihapus dari sini.');
        }

        $user->delete();

        return redirect()->route('admin.peserta.index')->with('success', 'Data peserta berhasil dihapus.');
    }
}