<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ApprenticeProfile;
use App\Models\AttendanceSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUser80HoursSeeder extends Seeder
{
    /**
     * 80 horas = 4800 minutos
     */
    public function run(): void
    {
        $apprenticeRole = Role::where('name', 'Aprendiz')->first();

        if (!$apprenticeRole) {
            $this->command->error('Role "Aprendiz" no encontrado. Ejecuta RoleSeeder primero.');
            return;
        }

        // Crear usuario de prueba con 80 horas
        $user = User::firstOrCreate(
            ['email' => 'test80h@example.com'],
            [
                'role_id' => $apprenticeRole->id,
                'full_name' => 'Usuario Prueba 80h',
                'password' => Hash::make('password'),
                'status' => 'activo'
            ]
        );

        // Crear perfil de aprendiz
        ApprenticeProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'document_number' => '99999999',
                'phone' => '3009999999',
                'cohort' => 'TEST-80H',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31'
            ]
        );

        // Crear 16 sesiones de 5 horas cada una (80 horas totales)
        // 5 horas = 300 minutos
        $startDate = Carbon::create(2026, 1, 15, 8, 0);
        
        for ($i = 0; $i < 16; $i++) {
            $start = $startDate->clone()->addDays($i);
            $end = $start->clone()->addMinutes(300);

            AttendanceSession::updateOrCreate(
                [
                    'apprentice_id' => $user->id,
                    'start_at' => $start,
                ],
                [
                    'end_at' => $end,
                    'duration_minutes' => 300
                ]
            );
        }

        $this->command->info("✓ Usuario creado: test80h@example.com");
        $this->command->info("✓ Total de horas: 80h (16 sesiones × 5h)");
        $this->command->info("✓ Contraseña: password");
    }
}
