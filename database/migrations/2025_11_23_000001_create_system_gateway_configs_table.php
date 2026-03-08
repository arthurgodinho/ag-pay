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
        Schema::create('system_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name')->unique();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->boolean('is_active_for_pix')->default(false);
            $table->boolean('is_active_for_card')->default(false);
            $table->boolean('is_default_for_pix')->default(false);
            $table->boolean('is_default_for_card')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_gateway_configs');
    }
};
