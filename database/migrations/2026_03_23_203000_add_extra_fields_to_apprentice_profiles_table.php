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
        Schema::table('apprentice_profiles', function (Blueprint $table) {
            $table->string('personal_email')->nullable()->after('user_id');
            $table->string('blood_type', 10)->nullable()->after('phone'); // RH
            $table->string('emergency_contact', 100)->nullable()->after('blood_type');
            $table->string('residence_address')->nullable()->after('emergency_contact');
            $table->string('job_title', 100)->nullable()->after('residence_address');
            $table->string('technical_advisor', 150)->nullable()->after('job_title');
            $table->string('fiche_number', 50)->nullable()->after('cohort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apprentice_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'personal_email',
                'blood_type',
                'emergency_contact',
                'residence_address',
                'job_title',
                'technical_advisor',
                'fiche_number'
            ]);
        });
    }
};
