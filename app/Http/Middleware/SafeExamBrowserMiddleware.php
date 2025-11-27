<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SafeExamBrowserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has('X-SafeExamBrowser-RequestHash')) {
            abort(403, 'Akses hanya dapat dilakukan melalui Safe Exam Browser');
        }

        return $next($request);
    }
}
