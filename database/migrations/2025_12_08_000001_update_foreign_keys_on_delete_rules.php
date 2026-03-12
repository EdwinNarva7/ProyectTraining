<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('apprentice_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('apprentice_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->foreign('apprentice_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('apprentice_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('login_audit', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('apprentice_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('apprentice_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->foreign('apprentice_id')->references('id')->on('users');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['apprentice_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('apprentice_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::table('login_audit', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};

