<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestResult; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TestAccessController extends Controller
{
    /**
     * Menampilkan halaman awal untuk memasukkan kode tes.
     */
    public function showCodeForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses kode tes, jika valid, arahkan ke halaman pengisian nama.
     */
    public function processCode(Request $request)
    {
        $request->validate(['test_code' => 'required|string']);
        $code = strtoupper($request->test_code);

        $test = Test::where('test_code', $code)
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('available_from')
                  ->orWhere(function($sub) {
                      $sub->where('available_from', '<=', now())
                          ->where('available_to', '>=', now());
                  });
            })
            ->first();

        if (!$test) {
            return redirect()->back()->with('error', 'Kode tes tidak valid, tidak aktif, atau sudah lewat jadwal.');
        }

        Session::put('accessed_test_code', $code);
        return redirect()->route('test-code.name');
    }

    /**
     * Menampilkan halaman untuk mengisi nama peserta.
     */
    public function showNameForm()
    {
        if (!Session::has('accessed_test_code')) {
            return redirect()->route('login');
        }
        return view('auth.enter-name');
    }

    /**
     * Menyimpan data diri peserta di session dan memulai tes.
     */
    public function startTest(Request $request)
    {
        $code = Session::get('accessed_test_code');
        if (!$code) {
            return redirect()->route('login');
        }
        
        $test = Test::where('test_code', $code)->firstOrFail();

        $validatedData = $request->validate([
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'education' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
        ]);

        // --- LOGIKA PENCEGAHAN BARU ---
        // Cek apakah email ini sudah pernah mengerjakan tes ini
        $existingResult = TestResult::where('test_id', $test->id)
                                    ->where('participant_email', $validatedData['participant_email'])
                                    ->exists();

        if ($existingResult) {
            return redirect()->route('login')->with('error', 'Anda sudah pernah menyelesaikan tes ini sebelumnya.');
        }
        // --- AKHIR LOGIKA PENCEGAHAN ---


        Session::put('participant_data', $validatedData);
        Session::put('active_test_id', $test->id);

        return redirect()->route('tests.show', $test);
    }
}