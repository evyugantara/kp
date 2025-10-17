<?php
// ======================================================================
// FILE: app/Http/Middleware/EnsurePengelola.php  (NEW)
// ======================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePengelola
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (auth()->user()->role ?? 'penghuni') !== 'pengelola') {
            abort(403, 'Hanya untuk pengelola.');
        }
        return $next($request);
    }
}
