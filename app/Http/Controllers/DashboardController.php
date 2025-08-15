<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TestCategory;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna dengan daftar tes yang tersedia.
     */
    public function index(Request $request)
    {
        $categories = TestCategory::all();

        // Mulai query untuk mengambil tes yang sudah di-publish
        $query = Test::where('is_published', true);

        // Jika ada input pencarian (search)
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Jika ada input filter kategori (category)
        if ($request->has('category') && $request->category != '') {
            $query->where('test_category_id', $request->category);
        }

        $tests = $query->latest()->paginate(9)->withQueryString();

        return view('dashboard', compact('tests', 'categories'));
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
