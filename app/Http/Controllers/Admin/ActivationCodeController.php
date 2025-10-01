<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Test;
use Illuminate\Http\Request;

class ActivationCodeController extends Controller
{
    /**
     * Menampilkan halaman untuk generate kode aktivasi.
     */
    public function index()
    {
        // Ambil semua tes yang bukan template untuk ditampilkan di dropdown
        $tests = Test::where('is_template', false)->orderBy('title')->get();
        
        return view('admin.codes.index', compact('tests'));
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

        for ($i = 0; $i < $request->quantity; $i++) {
            $code = ActivationCode::create([
                'test_id' => $test->id,
                // Kode akan di-generate otomatis oleh boot method di model
                'expires_at' => now()->addHours(24),
            ]);
            $generatedCodes[] = $code->code;
        }

        return redirect()->route('admin.codes.index')
                         ->with('success', $request->quantity . ' kode aktivasi berhasil dibuat untuk tes: ' . $test->title)
                         ->with('generated_codes', $generatedCodes);
    }
}