<?php

use App\Mail\CertificateSentMail;
use App\Models\Certificate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

it('envía certificado por email y registra estado', function () {
    Storage::fake('local');
    Mail::fake();

    $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
    $apprenticeRole = Role::firstOrCreate(['name' => 'Aprendiz']);

    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'full_name' => 'Admin Test',
        'email' => 'admin@test.com',
        'status' => 'activo',
    ]);

    $apprentice = User::factory()->create([
        'role_id' => $apprenticeRole->id,
        'full_name' => 'Aprendiz Test',
        'email' => 'apprentice@test.com',
        'status' => 'activo',
    ]);

    $pdfPath = 'certificates/test_cert.pdf';
    Storage::put($pdfPath, 'PDF');

    $certificate = Certificate::create([
        'apprentice_id' => $apprentice->id,
        'hours_completed' => 120,
        'issued_at' => now(),
        'status' => 'generado',
        'pdf_path' => $pdfPath,
        'email_to' => 'destinatario@test.com',
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.certificates.send', $certificate))
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    Mail::assertSent(CertificateSentMail::class, function ($mailable) use ($certificate) {
        return $mailable->certificate->id === $certificate->id;
    });

    $certificate->refresh();
    expect($certificate->status)->toBe('enviado');
    expect($certificate->email_to)->toBe('destinatario@test.com');
    expect($certificate->email_sent_at)->not()->toBeNull();
});
