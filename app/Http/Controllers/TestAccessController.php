<?php

namespace App\Http\Controllers;

use App\Models\Test;
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

        // Cari tes yang aktif dan sesuai dengan kode
        $test = Test::where('test_code', $code)
            ->where('is_published', true)
            ->where(function ($q) {
                // Logika pengecekan jadwal:
                // Tes valid jika tidak ada jadwal (selalu tersedia)
                $q->whereNull('available_from')
                  // ATAU jika waktu sekarang berada di dalam rentang jadwal
                  ->orWhere(function($sub) {
                      $sub->where('available_from', '<=', now())
                          ->where('available_to', '>=', now());
                  });
            })
            ->first();

        // Jika tes tidak ditemukan atau tidak aktif/terjadwal, kembali dengan error
        if (!$test) {
            return redirect()->back()->with('error', 'Kode tes tidak valid, tidak aktif, atau sudah lewat jadwal.');
        }

        // Jika valid, simpan kode di session dan arahkan ke form nama
        Session::put('accessed_test_code', $code);
        
        return redirect()->route('test-code.name');
    }

    /**
     * Menampilkan halaman untuk mengisi nama peserta.
     */
    public function showNameForm()
    {
        // Pastikan pengguna sudah melewati langkah 1 (memvalidasi kode)
        $code = Session::get('accessed_test_code');
        if (!$code) {
            return redirect()->route('login');
        }
        
        return view('auth.enter-name');
    }

    /**
     * Menyimpan nama peserta di session dan memulai tes.
     */
    public function startTest(Request $request)
    {
        $code = Session::get('accessed_test_code');
        if (!$code) {
            return redirect()->route('login');
        }

        // Validasi semua data diri yang dimasukkan
        $validatedData = $request->validate([
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'education' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
        ]);

        // Simpan semua data diri ke dalam satu array di session
        Session::put('participant_data', $validatedData);

        $test = Test::where('test_code', $code)->firstOrFail();
        
        // Simpan ID tes di session untuk validasi di halaman pengerjaan
        Session::put('active_test_id', $test->id);

        // Arahkan ke halaman pengerjaan tes
        return redirect()->route('tests.show', $test);
    }
}

