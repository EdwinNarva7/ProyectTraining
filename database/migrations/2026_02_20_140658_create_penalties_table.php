<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprentice_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->decimal('scheduled_hours', 5, 2);
            $table->decimal('attended_hours', 5, 2);
            $table->decimal('penalty_hours', 5, 2);
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('set null');
            $table->enum('status', ['pending', 'in_recovery', 'closed'])->default('pending');
            $table->timestamps();

            $table->index(['apprentice_id', 'date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
