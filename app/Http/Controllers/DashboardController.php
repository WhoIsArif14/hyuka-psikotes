<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna dengan daftar tes yang tersedia.
     */
    public function index()
    {
        $tests = Test::where('is_published', true)->latest()->paginate(9);
        return view('dashboard', compact('tests'));
    }

    /**
     * Menampilkan halaman riwayat tes pengguna.
     */
    public function history()
    {
        // Ambil semua hasil tes milik user yang sedang login
        $results = TestResult::where('user_id', Auth::id())
                              ->with('test') // 'with' untuk mengambil info tes (judul, dll)
                              ->latest()
                              ->paginate(10);

        return view('my-results', compact('results'));
    }
}