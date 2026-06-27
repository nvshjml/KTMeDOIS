<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table): void {
            $table->foreignId('cust_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cust_id');
        });
    }
};
