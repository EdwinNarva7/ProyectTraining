<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricLoginController extends Controller
{
    /**
     * Muestra el panel de ingreso biométrico (escaneo de huella).
     */
    public function biometricPanel()
    {
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->orderBy('full_name')->get();

        return view('auth.biometric-login', compact('apprentices'));
    }

    /**
     * Autentica a un usuario mediante huella (desde Bridge C#).
     * POST /biometric-login/fingerprint
     */
    public function fingerprintLogin(Request $request)
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

        Auth::login($user, true);

        $mode = $request->input('mode', 'system');
        $redirectUrl = route('dashboard');
        
        if ($user->role) {
            if ($user->role->name === 'Administrador') {
                $redirectUrl = route('admin.dashboard');
            } elseif ($user->role->name === 'Aprendiz') {
                if ($mode === 'attendance') {
                    $redirectUrl = route('apprentice.attendance.index');
                } else {
                    $redirectUrl = route('apprentice.dashboard');
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bienvenido, {$user->full_name}.",
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Autentica a un usuario desde la lista manual (sin contraseña, sin huella temporalmente)
     * POST /biometric-login/manual
     */
    public function manualLogin(Request $request)
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

        Auth::login($user, true);

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
            'message' => "Inicio de sesión manual exitoso. Bienvenido, {$user->full_name}.",
            'redirect' => $redirectUrl
        ]);
    }
}
