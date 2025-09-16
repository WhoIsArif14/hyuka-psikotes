<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- DATA STATISTIK DASAR ---
        $totalProcess = TestResult::count(); // Total peserta yang telah mengerjakan
        $psikogramCreated = Test::where('is_template', false)->count(); // Total tes (psikogram) yang dibuat

        // --- DATA CONTOH UNTUK KUOTA & LANGGANAN ---
        // Nanti, data ini bisa diambil dari database langganan Anda
        $kuotaPeserta = 500;
        $sisaKuotaPeserta = $kuotaPeserta - $totalProcess;

        $kuotaPsikogram = 20;
        $sisaKuotaPsikogram = $kuotaPsikogram - $psikogramCreated;
        
        $subscriptionEndDate = Carbon::now()->addDays(45); // Contoh: Langganan berakhir 45 hari dari sekarang
        $expiredInDays = now()->diffInDays($subscriptionEndDate);
        // --- AKHIR DATA CONTOH ---

        // Data untuk Rangkuman Pelaksanaan Psikotes
        // Mengambil tes yang sudah pernah dikerjakan, diurutkan dari yang terbaru
        $rangkumanTes = Test::where('is_template', false)
                            ->has('testResults') // Hanya ambil tes yang punya hasil
                            ->withCount('testResults') // Hitung jumlah pengerjaan
                            ->latest('updated_at')
                            ->take(5) // Ambil 5 teratas
                            ->get();

        return view('admin.dashboard', compact(
            'totalProcess',
            'kuotaPeserta',
            'sisaKuotaPeserta',
            'psikogramCreated',
            'kuotaPsikogram',
            'sisaKuotaPsikogram',
            'expiredInDays',
            'rangkumanTes'
        ));
    }
}

