<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Test;
use Illuminate\Http\Request;

class ActivationCodeController extends Controller
{
    /**
     * Menampilkan halaman untuk generate kode aktivasi dan daftar kode yang sudah ada.
     */
    public function index()
    {
        // 1. Ambil semua tes yang bukan template untuk form pembuatan kode
        $tests = Test::where('is_template', false)->orderBy('title')->get();

        // 2. Ambil daftar kode aktivasi yang sudah ada untuk ditampilkan di tabel
        // Memuat relasi 'test' (Modul) untuk menampilkan nama Modul di tabel
        $codes = ActivationCode::with('test')
            ->latest()
            ->paginate(10); // Pagination 10 baris per halaman

        // Kirimkan kedua variabel ke view
        return view('admin.codes.index', compact('tests', 'codes'));
    }

    /**
     * Membuat kode aktivasi baru secara massal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'quantity' => 'required|integer|min:1|max:1000', // Batasi maksimal 1000 kode per request
        ]);

        $test = Test::findOrFail($request->test_id);
        $generatedCodes = [];

        // Note: Anda perlu menambahkan logic DB transaction di sini untuk keamanan data
        for ($i = 0; $i < $request->quantity; $i++) {
            $code = ActivationCode::create([
                'test_id' => $test->id,
                // Asumsikan kode di-generate oleh model dan kolom 'code' diisi
                'expires_at' => now()->addHours(24),
            ]);
            $generatedCodes[] = $code->code;
        }

        return redirect()->route('admin.codes.index')
            ->with('success', $request->quantity . ' kode aktivasi berhasil dibuat untuk tes: ' . $test->title)
            ->with('generated_codes', $generatedCodes);
    }

    public function show($id)
    {
        $code = ActivationCode::findOrFail($id);

        // Jika ada parameter batch, ambil semua kode dalam batch tersebut
        if (request('batch')) {
            $batchKey = request('batch');

            // Cari semua kode dengan batch_id yang sama
            if (isset($code->batch_id)) {
                $batchCodes = ActivationCode::where('batch_id', $code->batch_id)
                    ->with('test')
                    ->orderBy('code')
                    ->get();
            } else {
                // Fallback: group by test_id dan waktu generate yang sama (dalam 1 menit)
                $timeRange = $code->created_at;
                $batchCodes = ActivationCode::where('test_id', $code->test_id)
                    ->whereBetween('created_at', [
                        $timeRange->copy()->subMinute(),
                        $timeRange->copy()->addMinute()
                    ])
                    ->with('test')
                    ->orderBy('code')
                    ->get();
            }
        } else {
            $batchCodes = collect([$code]);
        }

        return view('admin.activation_codes.show', [
            'code' => $code,
            'batchCodes' => $batchCodes,
        ]);
    }
}
