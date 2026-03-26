<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cargar la relación del rol si no está cargada
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            
            // Si el usuario es aprendiz, redirigir al dashboard de aprendiz
            if ($user->isApprentice()) {
                return redirect()->route('apprentice.dashboard');
            }
            
            // Si el usuario es administrador, redirigir al dashboard de admin
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Si el usuario es gerente, redirigir directo a su dashboard
            if ($user->isGerente()) {
                return redirect()->route('gerente.dashboard');
            }
        }

        return $next($request);
    }
}
