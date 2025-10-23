<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PapiKostickScore;

class PapiKostickResultController extends Controller
{
    // Asumsi ada Middleware 'admin' yang melindungi Controller ini
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar semua peserta yang sudah menyelesaikan tes
     */
    public function index()
    {
        // Ambil user yang sudah memiliki skor Papi Kostick
        $users = User::whereHas('papiScores')->paginate(15); 
        return view('papi.admin.results.index', compact('users'));
    }

    /**
     * Menampilkan detail hasil tes (skor & grafik) untuk user tertentu
     */
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        $scores = PapiKostickScore::where('user_id', $userId)->get();

        if ($scores->isEmpty()) {
            return back()->with('error', 'Peserta ini belum menyelesaikan tes PAPI Kostick.');
        }

        return view('papi.admin.results.detail', compact('user', 'scores'));
    }

    // Anda bisa menambahkan method 'export' untuk export hasil ke PDF/Excel
    // public function export($userId) { ... }
}