<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Pakai Auth::user(), bukan Auth::karyawan()
        $user = Auth::user(); 

        if ($user->role == $role) {
            return $next($request);
        }

        return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
