<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivationCode;
use App\Models\TestResult;
use App\Models\Test;
use App\Models\AlatTes;
use App\Models\PapiResult;
use App\Models\RmibResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar batch Kode Aktivasi (Indeks Laporan).
     * Route: admin.reports.index
     */
    public function index(Request $request)
    {
        // Available tests for filter dropdown
        $tests = Test::select('id', 'title')->orderBy('title')->get();

        // Mengambil daftar semua kode aktivasi yang sudah terpakai (memiliki user_id)
        $usedCodes = ActivationCode::whereNotNull('user_id')
            ->when($request->input('q'), function ($query, $q) {
                return $query->where('code', 'like', "%{$q}%");
            })
            ->when($request->input('test_id'), function ($query, $testId) {
                return $query->where('test_id', $testId);
            })
            ->with(['user.latestTestResult', 'test']) // Preload user dan hasil tes terbaru
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Kita akan menggunakan view index untuk menampilkan daftar kode terpakai
        return view('admin.reports.index', compact('usedCodes', 'tests'));
    }

    /**
     * Hapus sebuah activation code.
     */
    public function destroy(ActivationCode $code)
    {
        try {
            $code->delete();
            return redirect()->route('admin.reports.index')->with('success', 'Kode aktivasi berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete activation code: ' . $e->getMessage());
            return redirect()->route('admin.reports.index')->with('error', 'Gagal menghapus kode aktivasi.');
        }
    }

    /**
     * Bulk delete selected activation codes.
     */
    public function bulkDestroy(Request $request)
    {
        // Debug logging: catat payload dan user agar kita tahu apakah permintaan sampai
        \Log::info('bulkDestroy called', [
            'user_id' => auth()->id() ?? 'guest',
            'payload' => $request->all(),
            'ip' => $request->ip(),
            'method' => $request->method(),
        ]);

        $ids = $request->input('selected', []);

        if (empty($ids)) {
            return redirect()->route('admin.reports.index')->with('error', 'Tidak ada kode yang dipilih.');
        }

        try {
            ActivationCode::whereIn('id', $ids)->delete();
            return redirect()->route('admin.reports.index')->with('success', 'Kode aktivasi terpilih berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Bulk delete activation codes failed: ' . $e->getMessage());
            return redirect()->route('admin.reports.index')->with('error', 'Gagal menghapus kode aktivasi terpilih.');
        }
    }

    /**
     * Menampilkan detail peserta yang menggunakan kode dari batch tertentu.
     * Route: admin.reports.show
     */
    public function show(ActivationCode $code) // Asumsi Route Model Binding pada model ActivationCode
    {
        // Ambil data peserta (users) yang terkait dengan batch ini
        // Jika batch_name tersedia, ambil semua activation codes dengan batch_name yang sama
        // lalu ambil relasi user dan hasil tes terbaru (latestTestResult)
        if (!empty($code->batch_name)) {
            $activations = ActivationCode::where('batch_name', $code->batch_name)
                ->whereNotNull('user_id')
                ->with('user.latestTestResult')
                ->get();

            // Ambil users dari setiap activation code
            $participants = $activations->map(function ($act) {
                return $act->user;
            })->filter();
        } else {
            // Jika tidak ada batch_name, tampilkan user untuk kode ini saja (jika ada)
            $participants = collect();
            if ($code->user) {
                // Muat relasi latestTestResult
                $code->load('user.latestTestResult');
                $participants = collect([$code->user]);
            }
        }

        return view('admin.reports.show', compact('code', 'participants'));
    }

    /**
     * Menampilkan laporan lengkap per peserta dengan semua hasil tes.
     * Route: admin.reports.participant
     */
    public function participantReport($userId)
    {
        $user = User::with(['testResults.test', 'testResults.alatTes'])->findOrFail($userId);

        // Get all test results for this user
        $testResults = TestResult::where('user_id', $userId)
            ->with(['test', 'alatTes'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get PAPI results
        $papiResults = PapiResult::where('user_id', $userId)
            ->with('test')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get RMIB results
        $rmibResults = RmibResult::where('user_id', $userId)
            ->with('test')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get activation codes used
        $activationCodes = ActivationCode::where('user_id', $userId)
            ->with('test')
            ->get();

        return view('admin.reports.participant', compact(
            'user',
            'testResults',
            'papiResults',
            'rmibResults',
            'activationCodes'
        ));
    }

    /**
     * Menampilkan laporan per alat tes (semua peserta yang mengerjakan alat tes tertentu).
     * Route: admin.reports.alat-tes
     */
    public function alatTesReport($alatTesId)
    {
        $alatTes = AlatTes::findOrFail($alatTesId);

        // Get all test results for this alat tes
        $testResults = TestResult::where('alat_tes_id', $alatTesId)
            ->with(['user', 'test'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total_participants' => TestResult::where('alat_tes_id', $alatTesId)->distinct('user_id')->count('user_id'),
            'avg_score' => TestResult::where('alat_tes_id', $alatTesId)->avg('score'),
            'max_score' => TestResult::where('alat_tes_id', $alatTesId)->max('score'),
            'min_score' => TestResult::where('alat_tes_id', $alatTesId)->min('score'),
            'completed_count' => TestResult::where('alat_tes_id', $alatTesId)->where('status', 'Completed')->count(),
        ];

        // Check if this is PAPI or RMIB
        $isPapi = str_contains(strtolower($alatTes->slug ?? $alatTes->name), 'papi');
        $isRmib = str_contains(strtolower($alatTes->slug ?? $alatTes->name), 'rmib');

        $papiResults = collect();
        $rmibResults = collect();

        if ($isPapi) {
            $papiResults = PapiResult::with(['user', 'test'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        if ($isRmib) {
            $rmibResults = RmibResult::with(['user', 'test'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('admin.reports.alat-tes', compact(
            'alatTes',
            'testResults',
            'stats',
            'isPapi',
            'isRmib',
            'papiResults',
            'rmibResults'
        ));
    }

    /**
     * Export laporan ke Excel (per alat tes).
     * Route: admin.reports.export
     */
    public function exportReport(Request $request)
    {
        $testId = $request->input('test_id');
        $alatTesId = $request->input('alat_tes_id');

        $query = TestResult::with(['user', 'test', 'alatTes']);

        if ($testId) {
            $query->where('test_id', $testId);
        }

        if ($alatTesId) {
            $query->where('alat_tes_id', $alatTesId);
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'laporan_hasil_tes_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'No',
                'Nama Peserta',
                'Email',
                'No. HP',
                'Pendidikan',
                'Jurusan',
                'Modul Tes',
                'Alat Tes',
                'Skor',
                'IQ',
                'Status',
                'Waktu Selesai',
                'Durasi Pengerjaan'
            ]);

            // CSV Data
            $no = 1;
            foreach ($results as $result) {
                fputcsv($file, [
                    $no++,
                    $result->participant_name ?? $result->user->name ?? '-',
                    $result->email ?? $result->user->email ?? '-',
                    $result->phone_number ?? $result->user->phone_number ?? '-',
                    $result->education ?? $result->user->education ?? '-',
                    $result->major ?? $result->user->major ?? '-',
                    $result->test->name ?? '-',
                    $result->alatTes->name ?? '-',
                    $result->score ?? 0,
                    $result->iq ?? '-',
                    $result->status ?? '-',
                    $result->completed_at ?? $result->created_at,
                    $result->duration_seconds ? gmdate('H:i:s', $result->duration_seconds) : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Membuat dan mengunduh laporan hasil psikotes dalam format PDF.
     * Route: admin.reports.pdf
     */
    public function generatePdfReport(TestResult $testResult)
    {
        // Cari hasil PAPI terkait (jika ada)
        $papiResult = null;
        try {
            $papiResult = \App\Models\PapiResult::where('user_id', $testResult->user_id)
                ->where('test_id', $testResult->test_id)
                ->first();
        } catch (\Throwable $e) {
            // ignore: jika model atau relasi tidak ada
        }

        // ✅ Ambil kode aktivasi yang digunakan peserta untuk tes ini
        $activationCode = ActivationCode::where('user_id', $testResult->user_id)
            ->where('test_id', $testResult->test_id)
            ->first();

        // ✅ Hitung Statistik Jawaban (Benar/Salah)
        // Load relasi alatTes dan questions untuk mendapatkan total soal
        $testResult->load('alatTes.questions');

        $totalQuestions = 0;
        if ($testResult->alatTes) {
            $totalQuestions = $testResult->alatTes->questions->count();
        }
        $correctCount = (int) $testResult->score; // Asumsi: Skor = Jumlah Benar
        $wrongCount = max(0, $totalQuestions - $correctCount);

        $orderedScores = [];
        if ($papiResult) {
            $orderedScores = $papiResult->getOrderedScores();
        }

        // Jika Dompdf tidak terinstal, beri pesan jelas ke admin untuk menjalankan composer require
        if (!class_exists(\Dompdf\Dompdf::class)) {
            return redirect()->back()->with('error', 'Library PDF (dompdf) belum terpasang. Jalankan: composer require dompdf/dompdf dan jalankan composer install.');
        }

        try {
            $html = view('pdf.test_result_report', compact('testResult', 'papiResult', 'orderedScores', 'activationCode', 'totalQuestions', 'correctCount', 'wrongCount'))->render();

            $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'Laporan_' . ($testResult->user->name ?? ($testResult->participant_name ?? 'unknown')) . '_' . time() . '.pdf';

            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat laporan PDF: ' . $e->getMessage());
        }
    }
}
