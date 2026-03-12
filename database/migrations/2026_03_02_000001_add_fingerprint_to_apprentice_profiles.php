<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apprentice_profiles', function (Blueprint $table) {
            // Almacena la plantilla de huella en Base64 (el template serializado del SDK DPFP/DPUruNet)
            $table->longText('fingerprint_template')->nullable()->after('end_date');
            $table->timestamp('fingerprint_enrolled_at')->nullable()->after('fingerprint_template');
        });
    }

    public function down(): void
    {
        Schema::table('apprentice_profiles', function (Blueprint $table) {
            $table->dropColumn(['fingerprint_template', 'fingerprint_enrolled_at']);
        });
    }
};
