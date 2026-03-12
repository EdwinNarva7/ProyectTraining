<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\ApprenticeProfile;
use App\Models\AttendanceSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CreateTestApprentice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apprentice:create-test-account';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Crea un aprendiz de prueba con 80 horas de asistencia para testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Creando aprendiz de prueba...');

        try {
            // Obtener rol de aprendiz
            $apprenticeRole = Role::where('name', 'Aprendiz')->first();

            if (!$apprenticeRole) {
                $this->error('❌ Error: No existe el rol "Aprendiz" en la base de datos');
                return 1;
            }

            // Datos del aprendiz de prueba
            $email = 'test.apprentice@example.com';
            
            // Verificar si ya existe
            if (User::where('email', $email)->exists()) {
                $this->warn('⚠️ El aprendiz de prueba ya existe');
                $user = User::where('email', $email)->first();
                $this->info("ID: {$user->id} | Email: {$user->email}");
                return 0;
            }

            // Crear usuario
            $user = User::create([
                'role_id' => $apprenticeRole->id,
                'full_name' => 'Aprendiz Prueba',
                'email' => $email,
                'password' => Hash::make('password123'),
                'status' => 'activo'
            ]);

            $this->info("✅ Usuario creado: {$user->full_name} (ID: {$user->id})");

            // Crear perfil de aprendiz
            ApprenticeProfile::create([
                'user_id' => $user->id,
                'document_number' => '9999999999',
                'phone' => '3001234567',
                'cohort' => 'TEST-2025-1',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(30)
            ]);

            $this->info("✅ Perfil de aprendiz creado");

            // Crear 80 horas en sesiones de asistencia
            // Dividiremos en 10 sesiones de 8 horas cada una (distribuyendo en los últimos 20 días)
            $startDate = Carbon::now()->subDays(20);
            
            for ($i = 0; $i < 10; $i++) {
                $sessionStart = $startDate->addDay()->setHour(8)->setMinute(0)->setSecond(0);
                $sessionEnd = $sessionStart->copy()->addHours(8);

                AttendanceSession::create([
                    'apprentice_id' => $user->id,
                    'start_at' => $sessionStart,
                    'end_at' => $sessionEnd,
                    'duration_minutes' => 480, // 8 horas = 480 minutos
                ]);
            }

            $this->info("✅ 80 horas de asistencia registradas (10 sesiones de 8 horas)");

            $this->newLine();
            $this->line('═════════════════════════════════════════');
            $this->line('📋 DATOS DEL APRENDIZ DE PRUEBA');
            $this->line('═════════════════════════════════════════');
            $this->line("✉️  Email:      <comment>{$email}</comment>");
            $this->line("🔑 Contraseña:  <comment>password123</comment>");
            $this->line("👤 ID Usuario:  <comment>{$user->id}</comment>");
            $this->line("📚 Cohorte:     <comment>TEST-2025-1</comment>");
            $this->line("⏱️  Horas:      <comment>80 horas</comment>");
            $this->line('═════════════════════════════════════════');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
