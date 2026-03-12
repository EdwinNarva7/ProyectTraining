<?php

use App\Models\Certificate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('envía certificado usando el mailer configurado y actualiza estado', function () {
    $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
    $apprenticeRole = Role::firstOrCreate(['name' => 'Aprendiz']);

    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'full_name' => 'Admin E2E',
        'email' => 'admin.e2e@test.com',
        'status' => 'activo',
    ]);

    $apprentice = User::factory()->create([
        'role_id' => $apprenticeRole->id,
        'full_name' => 'Aprendiz E2E',
        'email' => 'apprentice.e2e@test.com',
        'status' => 'activo',
    ]);

    $certificate = Certificate::create([
        'apprentice_id' => $apprentice->id,
        'hours_completed' => 120,
        'issued_at' => now(),
        'status' => 'generado',
        'email_to' => 'usuario1random1@gmail.com',
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.certificates.generate', $certificate))
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    $certificate->refresh();
    expect($certificate->pdf_path)->not()->toBeNull();
    expect(Storage::exists($certificate->pdf_path))->toBeTrue();

    $this->actingAs($admin)
        ->post(route('admin.certificates.send', $certificate))
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    $certificate->refresh();
    expect($certificate->status)->toBe('enviado');
    expect($certificate->email_to)->toBe('usuario1random1@gmail.com');
    expect($certificate->email_sent_at)->not()->toBeNull();
});
