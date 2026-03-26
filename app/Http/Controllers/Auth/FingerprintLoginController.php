<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FingerprintLoginController extends Controller
{
    /**
     * Autentica a un usuario mediante su ID (previament identificado por el bridge).
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::with('role')->find($request->user_id);

        if (!$user || $user->status !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no está activo o no existe.',
            ], 403);
        }

        // Iniciar sesión
        Auth::login($user, true); // true para "recordarme"

        // Determinar redirección según rol
        $redirectUrl = route('dashboard');
        if ($user->role) {
            if ($user->role->name === 'Administrador') {
                $redirectUrl = route('admin.dashboard');
            } elseif ($user->role->name === 'Aprendiz') {
                $redirectUrl = route('apprentice.dashboard');
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bienvenido de nuevo, {$user->full_name}.",
            'redirect' => $redirectUrl
        ]);
    }
}
