<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\User;
use App\Models\Client;
use App\Models\AlatTes;
use App\Models\ActivationCode;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- STATISTIK UTAMA ---
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalKlien = Client::count();
        $totalModul = Test::where('is_template', false)->count();
        $totalAlatTes = AlatTes::count();

        // --- STATISTIK KODE AKTIVASI ---
        $totalKodeAktivasi = ActivationCode::count();
        $kodeAktivasiTerpakai = ActivationCode::where('status', 'used')->count();
        $kodeAktivasi = ActivationCode::whereNull('used_at')->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();

        // --- STATISTIK HARI INI ---
        $pesertaHariIni = TestResult::whereDate('created_at', today())->count();
        $tesSedangBerlangsung = TestResult::whereNotNull('start_time')
            ->whereNull('end_time')
            ->count();

        // --- AKTIVITAS TERBARU ---
        $aktivitasTerbaru = TestResult::with(['user', 'test'])
            ->latest()
            ->take(5)
            ->get();

        // --- DATA GRAFIK: Peserta per hari (7 hari terakhir) ---
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = TestResult::whereDate('created_at', $date)->count();
        }

        // --- RANGKUMAN TES POPULER ---
        $rangkumanTes = Test::where('is_template', false)
            ->withCount('testResults')
            ->orderByDesc('test_results_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPeserta',
            'totalKlien',
            'totalModul',
            'totalAlatTes',
            'totalKodeAktivasi',
            'kodeAktivasiTerpakai',
            'kodeAktivasi',
            'pesertaHariIni',
            'tesSedangBerlangsung',
            'aktivitasTerbaru',
            'chartLabels',
            'chartData',
            'rangkumanTes'
        ));
    }
}

