<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login untuk admin.
     */
    public function create(): View
    {
        return view('auth.login-admin');
    }

    /**
     * Menangani request login dari admin.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        
        // dd('DEBUG 1: Autentikasi berhasil!'); // Hapus komentar ini untuk tes pertama

        $user = Auth::user();

        // dd($user); // Hapus komentar ini untuk melihat seluruh data user
        // dd($user->role); // Hapus komentar ini untuk melihat role saja

        // Pengecekan hanya untuk admin
        if ($user && $user->role === 'admin') {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Jika bukan admin, logout dan kembali dengan error
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Hanya admin yang dapat login melalui halaman ini.',
        ])->onlyInput('email');
    }

    /**
     * Menghancurkan session (logout) untuk admin.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
