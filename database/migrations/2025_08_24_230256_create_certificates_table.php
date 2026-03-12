<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprentice_id')->constrained('users');
            $table->integer('hours_completed');
            $table->timestamp('issued_at')->useCurrent();
            $table->enum('status', ['generado', 'enviado', 'descargado', 'anulado'])->default('generado');
            $table->text('pdf_path')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->string('email_to', 190)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
