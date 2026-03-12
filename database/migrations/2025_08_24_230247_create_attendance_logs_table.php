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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprentice_id')->constrained('users');
            $table->enum('event_type', ['entrada', 'salida']);
            $table->timestamp('occurred_at')->useCurrent();
            $table->string('source', 40)->default('lector');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            
            $table->index(['apprentice_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
