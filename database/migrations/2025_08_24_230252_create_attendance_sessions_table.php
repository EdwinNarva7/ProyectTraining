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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprentice_id')->constrained('users');
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['apprentice_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
