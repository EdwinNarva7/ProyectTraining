<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrGerente
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('login');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        if (!$user->isAdmin() && !$user->isGerente()) {
            if ($user->isApprentice()) {
                return redirect()->route('apprentice.dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
            }
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
