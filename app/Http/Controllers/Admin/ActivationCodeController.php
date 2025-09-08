<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivationCodeController extends Controller
{
    /**
     * Membuat sejumlah kode aktivasi untuk sebuah tes.
     */
    public function store(Request $request, Test $test)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:500', // Batasi maksimal 500 sekali generate
        ]);

        $quantity = $request->input('quantity');
        $generatedCodes = [];

        for ($i = 0; $i < $quantity; $i++) {
            do {
                // Membuat kode unik (contoh: ABCD-EFGH)
                $code = Str::upper(Str::random(4) . '-' . Str::random(4));
            } while (ActivationCode::where('code', $code)->exists());

            ActivationCode::create([
                'test_id' => $test->id,
                'code' => $code,
                'expires_at' => now()->addHours(24),
            ]);
            $generatedCodes[] = $code;
        }

        // Simpan kode yang baru dibuat ke session untuk ditampilkan
        session()->flash('generated_codes', $generatedCodes);

        return redirect()->back()->with('success', "{$quantity} kode aktivasi berhasil dibuat.");
    }

    /**
     * Menghapus kode aktivasi.
     */
    public function destroy(ActivationCode $code)
    {
        $code->delete();
        return redirect()->back()->with('success', 'Kode aktivasi berhasil dihapus.');
    }
}

