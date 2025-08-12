<?php

namespace App\Http\Controllers;

use App\Models\Test; // <-- Impor model Test
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna dengan daftar tes yang tersedia.
     */
    public function index()
    {
        // Ambil semua tes yang sudah di-publish, urutkan dari yang terbaru
        $tests = Test::where('is_published', true)->latest()->paginate(9);

        // Kirim data tes ke view 'dashboard'
        return view('dashboard', compact('tests'));
    }
}