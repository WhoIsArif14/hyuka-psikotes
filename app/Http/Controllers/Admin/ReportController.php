<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivationCode; // Asumsi model Batch Kode Aktivasi Anda
use App\Models\TestResult; // Asumsi model Hasil Tes Anda
use Illuminate\Support\Facades\Log;
// use PDF; // Uncomment jika Anda menggunakan library PDF (misalnya, DomPDF)

class ReportController extends Controller
{
    /**
     * Menampilkan daftar batch Kode Aktivasi (Indeks Laporan).
     * Route: admin.reports.index
     */
    public function index()
    {
        // Mengambil daftar semua kode aktivasi yang sudah terpakai (memiliki user_id)
        $usedCodes = ActivationCode::whereNotNull('user_id')
            ->with(['user.latestTestResult', 'test']) // Preload user dan hasil tes terbaru
            ->latest()
            ->paginate(15);

        // Kita akan menggunakan view index untuk menampilkan daftar kode terpakai
        return view('admin.reports.index', compact('usedCodes'));
    }

    /**
     * Menampilkan detail peserta yang menggunakan kode dari batch tertentu.
     * Route: admin.reports.show
     */
    public function show(ActivationCode $code) // Asumsi Route Model Binding pada model ActivationCode
    {
        // Ambil data peserta (users) yang terkait dengan batch ini
        // dan preload hasil tes terbaru mereka (latestTestResult)
        $participants = $code->users()
            ->with('latestTestResult') // Pastikan relasi ini ada di model User/Peserta
            ->get();

        return view('admin.reports.show', compact('code', 'participants'));
    }

    /**
     * Membuat dan mengunduh laporan hasil psikotes dalam format PDF.
     * Route: admin.reports.pdf
     */
    public function generatePdfReport(TestResult $testResult) // Asumsi Route Model Binding pada TestResult
    {
        // --- LOGIKA PEMBUATAN PDF DITEMPATKAN DI SINI ---

        try {
            // Contoh Placeholder: Jika menggunakan library seperti DomPDF
            // $pdf = PDF::loadView('pdf.psikotest_report', compact('testResult'));
            // return $pdf->download('Laporan_' . $testResult->user->name . '_' . time() . '.pdf');

            // Respons sementara jika library PDF belum diimplementasikan
            return redirect()->back()->with('success', 'Laporan PDF untuk ' . $testResult->user->name . ' berhasil dibuat dan siap diunduh.');
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat laporan PDF: ' . $e->getMessage());
        }
    }
}
