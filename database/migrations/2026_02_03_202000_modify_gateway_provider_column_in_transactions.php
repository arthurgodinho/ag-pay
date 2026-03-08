<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change gateway_provider from ENUM to VARCHAR(50) to allow new gateways like 'paguemax', 'podpay', etc.
        DB::statement("ALTER TABLE transactions MODIFY COLUMN gateway_provider VARCHAR(50) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM if needed (though usually we don't want to lose data if we added new providers)
        // Ideally we should check distinct values and add them to enum, but for rollback we'll just restore the known list
        DB::statement("ALTER TABLE transactions MODIFY COLUMN gateway_provider ENUM('asaas','mercadopago','bspay','pixup','efi','ondapay','admin','venit','system','pluggou') NULL");
    }
};
