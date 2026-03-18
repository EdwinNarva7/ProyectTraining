<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ApprenticeProfile;
use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'apprenticeProfile.phase'])->orderBy('created_at', 'desc');

        $search = trim((string)$request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('apprenticeProfile', function ($p) use ($search) {
                      $p->where('document_number', 'like', '%' . $search . '%')
                        ->orWhere('cohort', 'like', '%' . $search . '%');
                  });
            });
        }

        $roleParam = $request->input('role');
        if ($roleParam) {
            $roleMap = [
                'admin' => 'Administrador',
                'aprendiz' => 'Aprendiz',
                'instructor' => 'Instructor',
            ];
            $roleName = $roleMap[$roleParam] ?? null;
            if ($roleName) {
                $query->whereHas('role', function ($q) use ($roleName) {
                    $q->where('name', $roleName);
                });
            }
        }

        $phaseId = $request->input('phase_id');
        $activePhase = Phase::where('is_active', true)->first();

        if ($phaseId || $activePhase) {
            $targetPhaseId = $phaseId ?: ($activePhase ? $activePhase->id : null);
            
            if ($targetPhaseId) {
                if (!$phaseId && $activePhase) {
                    $request->merge(['phase_id' => $activePhase->id]);
                }

                $query->where(function ($q) use ($targetPhaseId) {
                    // Mostrar aprendices de la fase seleccionada
                    $q->whereHas('apprenticeProfile', function ($p) use ($targetPhaseId) {
                        $p->where('phase_id', $targetPhaseId);
                    })
                    // O mostrar usuarios que NO son aprendices (Administradores, Instructores, etc.)
                    // Esto permite que el personal administrativo siga siendo visible
                    ->orWhereHas('role', function ($r) {
                        $r->where('name', '!=', 'Aprendiz');
                    });
                });
            }
        }

        $users = $query->paginate(15)->appends($request->query());
        $phases = Phase::all();

        return view('admin.users.index', compact('users', 'phases', 'activePhase'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $phases = Phase::all();
        $activePhase = Phase::where('is_active', true)->first();
        return view('admin.users.create', compact('roles', 'phases', 'activePhase'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $apprenticeRoleId = Role::where('name', 'Aprendiz')->value('id');
        $rules = [
            'role_id' => 'required|exists:roles,id',
            'full_name' => 'required|string|max:160',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|in:activo,inactivo',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phase_id' => 'nullable|exists:phases,id',
            'phone' => 'nullable|string|max:30',
            'cohort' => 'nullable|string|max:60',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ];
        $rules['document_number'] = ((int)$request->role_id === (int)$apprenticeRoleId)
            ? 'required|string|max:40|unique:apprentice_profiles,document_number'
            : 'nullable|string|max:40|unique:apprentice_profiles,document_number';
        $request->validate($rules);

        $userData = [
            'role_id' => $request->role_id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ];

        if ($request->hasFile('profile_photo')) {
            $userData['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user = User::create($userData);

        // Si es aprendiz, crear perfil
        if ((int)$request->role_id === (int)$apprenticeRoleId) {
            ApprenticeProfile::create([
                'user_id' => $user->id,
                'phase_id' => $request->phase_id,
                'document_number' => $request->document_number,
                'phone' => $request->phone,
                'cohort' => $request->cohort,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['role', 'apprenticeProfile', 'schedules', 'attendanceLogs', 'certificates'])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['apprenticeProfile'])->findOrFail($id);
        $roles = Role::all();
        $phases = Phase::all();

        return view('admin.users.edit', compact('user', 'roles', 'phases'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $apprenticeRoleId = Role::where('name', 'Aprendiz')->value('id');
        $documentRuleBase = [
            'string','max:40',
            Rule::unique('apprentice_profiles','document_number')
                ->ignore($user->apprenticeProfile?->user_id, 'user_id'),
        ];
        $rules = [
            'role_id' => 'required|exists:roles,id',
            'full_name' => 'required|string|max:160',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:activo,inactivo',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phase_id' => 'nullable|exists:phases,id',
            'phone' => 'nullable|string|max:30',
            'cohort' => 'nullable|string|max:60',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ];
        $rules['document_number'] = ((int)$request->role_id === (int)$apprenticeRoleId)
            ? array_merge(['required'], $documentRuleBase)
            : array_merge(['nullable'], $documentRuleBase);
        $request->validate($rules);

        $userData = [
            'role_id' => $request->role_id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->hasFile('profile_photo')) {
            // Eliminar foto anterior
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $userData['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($userData);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Actualizar perfil de aprendiz
        if ((int)$request->role_id === (int)$apprenticeRoleId) {
            if ($user->apprenticeProfile) {
                $user->apprenticeProfile->update([
                    'phase_id' => $request->phase_id,
                    'document_number' => $request->document_number,
                    'phone' => $request->phone,
                    'cohort' => $request->cohort,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
            } else {
                ApprenticeProfile::create([
                    'user_id' => $user->id,
                    'phase_id' => $request->phase_id,
                    'document_number' => $request->document_number,
                    'phone' => $request->phone,
                    'cohort' => $request->cohort,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
