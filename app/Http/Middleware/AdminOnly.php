<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Cargar la relación del rol si no está cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Solo permitir acceso a administradores
        if (!$user->isAdmin()) {
            // Si es aprendiz, redirigir a su dashboard
            if ($user->isApprentice()) {
                return redirect()->route('apprentice.dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
            }
            
            // Para otros roles, redirigir al dashboard principal
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
