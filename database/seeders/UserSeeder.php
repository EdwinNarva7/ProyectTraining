<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ApprenticeProfile;
use App\Models\Schedule;
use App\Models\Penalty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los roles
        $adminRole = Role::where('name', 'Administrador')->first();
        $apprenticeRole = Role::where('name', 'Aprendiz')->first();
        $gerenteRole = Role::where('name', 'Gerente')->first();

        // Crear usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@siap.com'],
            [
                'role_id' => $adminRole->id,
                'full_name' => 'Administrador Principal',
                'password' => Hash::make('password'),
                'status' => 'activo'
            ]
        );

        // Crear usuario gerente
        if ($gerenteRole) {
            User::firstOrCreate(
                ['email' => 'gerente@siap.com'],
                [
                    'role_id' => $gerenteRole->id,
                    'full_name' => 'Gerente Principal',
                    'password' => Hash::make('password'),
                    'status' => 'activo'
                ]
            );
        }

        // Crear algunos aprendices de ejemplo
        $apprentices = [
            ['full_name' => 'Juan Pérez',     'email' => 'juan.perez@example.com',     'document_number' => '12345678', 'phone' => '3001234567', 'cohort' => 'ADSO-2024-1', 'start_date' => '2024-01-15', 'end_date' => '2024-12-15'],
            ['full_name' => 'María García',   'email' => 'maria.garcia@example.com',   'document_number' => '87654321', 'phone' => '3007654321', 'cohort' => 'ADSO-2024-1', 'start_date' => '2024-01-15', 'end_date' => '2024-12-15'],
            ['full_name' => 'Carlos López',   'email' => 'carlos.lopez@example.com',   'document_number' => '11223344', 'phone' => '3001122334', 'cohort' => 'ADSO-2024-2', 'start_date' => '2024-02-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Ana Torres',     'email' => 'ana.torres@example.com',     'document_number' => '22334455', 'phone' => '3002233445', 'cohort' => 'ADSO-2024-2', 'start_date' => '2024-02-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Luis Martínez',  'email' => 'luis.martinez@example.com',  'document_number' => '33445566', 'phone' => '3003344556', 'cohort' => 'ADSO-2024-3', 'start_date' => '2024-03-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Sofía Ramírez',  'email' => 'sofia.ramirez@example.com',  'document_number' => '44556677', 'phone' => '3004455667', 'cohort' => 'ADSO-2024-3', 'start_date' => '2024-03-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Pedro Gómez',    'email' => 'pedro.gomez@example.com',    'document_number' => '55667788', 'phone' => '3005566778', 'cohort' => 'ADSO-2024-4', 'start_date' => '2024-04-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Daniela Cruz',   'email' => 'daniela.cruz@example.com',   'document_number' => '66778899', 'phone' => '3006677889', 'cohort' => 'ADSO-2024-4', 'start_date' => '2024-04-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Miguel Herrera', 'email' => 'miguel.herrera@example.com', 'document_number' => '77889900', 'phone' => '3007788990', 'cohort' => 'ADSO-2024-5', 'start_date' => '2024-05-01', 'end_date' => '2024-12-31'],
            ['full_name' => 'Laura Fernández','email' => 'laura.fernandez@example.com','document_number' => '88990011', 'phone' => '3008899001', 'cohort' => 'ADSO-2024-5', 'start_date' => '2024-05-01', 'end_date' => '2024-12-31'],
        ];

        foreach ($apprentices as $apprenticeData) {
            $user = User::firstOrCreate(
                ['email' => $apprenticeData['email']],
                [
                    'role_id' => $apprenticeRole->id,
                    'full_name' => $apprenticeData['full_name'],
                    'password' => Hash::make('password'),
                    'status' => 'activo'
                ]
            );

            ApprenticeProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'document_number' => $apprenticeData['document_number'],
                    'phone' => $apprenticeData['phone'],
                    'cohort' => $apprenticeData['cohort'],
                    'start_date' => $apprenticeData['start_date'],
                    'end_date' => $apprenticeData['end_date']
                ]
            );
        }

        // Crear 10 aprendices con deuda de 2 minutos (0.03h) para pruebas
        $debtApprentices = [];
        for ($i = 1; $i <= 10; $i++) {
            $debtApprentices[] = [
                'full_name' => "Aprendiz Deuda 2m #$i",
                'email' => "deuda2m{$i}@example.com",
                'document_number' => str_pad((90000000 + $i), 8, '0', STR_PAD_LEFT),
                'phone' => '300' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'cohort' => 'ADSO-TEST',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
            ];
        }

        $today = Carbon::today();
        $weekday = $today->dayOfWeek;
        $weekday = $weekday === 0 ? 7 : $weekday;

        foreach ($debtApprentices as $apprenticeData) {
            $user = User::firstOrCreate(
                ['email' => $apprenticeData['email']],
                [
                    'role_id' => $apprenticeRole->id,
                    'full_name' => $apprenticeData['full_name'],
                    'password' => Hash::make('password'),
                    'status' => 'activo'
                ]
            );

            ApprenticeProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'document_number' => $apprenticeData['document_number'],
                    'phone' => $apprenticeData['phone'],
                    'cohort' => $apprenticeData['cohort'],
                    'start_date' => $apprenticeData['start_date'],
                    'end_date' => $apprenticeData['end_date']
                ]
            );

            $schedule = Schedule::firstOrCreate(
                [
                    'apprentice_id' => $user->id,
                    'weekday' => $weekday,
                ],
                [
                    'start_time' => '08:00',
                    'end_time' => '09:00',
                    'status' => 'activo',
                ]
            );

            Penalty::firstOrCreate(
                [
                    'apprentice_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'scheduled_hours' => 1.00,
                    'attended_hours' => 0.97,
                    'penalty_hours' => 0.03,
                    'schedule_id' => $schedule->id,
                    'status' => 'pending',
                ]
            );
        }
    }
}
