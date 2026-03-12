<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'Administrador'],
            ['description' => 'Gestiona usuarios, horarios y certificados']
        );

        Role::firstOrCreate(
            ['name' => 'Aprendiz'],
            ['description' => 'Registra asistencia y descarga certificados']
        );
    }
}
