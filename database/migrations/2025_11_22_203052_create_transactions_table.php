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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->decimal('amount_gross', 15, 2);
            $table->decimal('amount_net', 15, 2);
            $table->decimal('fee', 15, 2);
            $table->string('status');
            $table->string('gateway_provider');
            $table->string('external_id')->nullable();
            $table->string('payer_email')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
