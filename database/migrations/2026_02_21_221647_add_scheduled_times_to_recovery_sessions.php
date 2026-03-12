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
        Schema::table('recovery_sessions', function (Blueprint $table) {
            $table->time('scheduled_start_time')->nullable()->after('date');
            $table->time('scheduled_end_time')->nullable()->after('scheduled_start_time');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'canceled'])->default('scheduled')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recovery_sessions', function (Blueprint $table) {
            $table->dropColumn(['scheduled_start_time', 'scheduled_end_time']);
            $table->enum('status', ['scheduled', 'completed', 'canceled'])->default('scheduled')->change();
        });
    }
};
