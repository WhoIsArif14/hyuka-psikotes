<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN rolenya adalah 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request); // Jika ya, lanjutkan request
        }

        // Jika tidak, kembalikan ke halaman dashboard user biasa atau halaman lain
        return redirect('/dashboard');
    }
}