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
        Schema::table('system_gateway_configs', function (Blueprint $table) {
            $table->string('pix_key')->nullable()->after('client_secret');
            $table->text('certificate_path')->nullable()->after('pix_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_gateway_configs', function (Blueprint $table) {
            $table->dropColumn(['pix_key', 'certificate_path']);
        });
    }
};
