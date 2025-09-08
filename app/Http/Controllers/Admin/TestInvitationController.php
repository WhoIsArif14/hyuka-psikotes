<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\User;
use App\Models\TestInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestInvitationController extends Controller
{
    /**
     * Mendaftarkan peserta baru ke tes dan membuat kode undangan.
     */
    public function store(Request $request, Test $test)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Cari atau buat pengguna baru dengan password acak
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => Hash::make(Str::random(10)),
            ]
        );

        // Buat kode login unik (cth: A8X2-B7C1)
        do {
            $loginCode = Str::upper(Str::random(4) . '-' . Str::random(4));
        } while (TestInvitation::where('login_code', $loginCode)->exists());

        // Buat undangan untuk tes
        TestInvitation::create([
            'test_id' => $test->id,
            'user_id' => $user->id,
            'login_code' => $loginCode,
        ]);

        return redirect()->back()->with('success', 'Peserta berhasil didaftarkan. Kode Login: ' . $loginCode);
    }

    /**
     * Menghapus undangan peserta dari sebuah tes.
     */
    public function destroy(TestInvitation $invitation)
    {
        $invitation->delete();
        return redirect()->back()->with('success', 'Undangan peserta berhasil dihapus.');
    }
}

