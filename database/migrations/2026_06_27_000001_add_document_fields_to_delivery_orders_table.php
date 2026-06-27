<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table): void {
            $table->date('order_date')->nullable()->after('po_number');
            $table->string('invoice_reference')->nullable()->after('order_date');
            $table->string('project_reference')->nullable()->after('invoice_reference');
            $table->text('shipping_address')->nullable()->after('project_reference');
            $table->text('invoice_address')->nullable()->after('shipping_address');
            $table->json('items')->nullable()->after('invoice_address');
            $table->date('delivery_date')->nullable()->after('items');
            $table->time('delivery_time')->nullable()->after('delivery_date');
            $table->text('remarks')->nullable()->after('delivery_time');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table): void {
            $table->dropColumn([
                'order_date',
                'invoice_reference',
                'project_reference',
                'shipping_address',
                'invoice_address',
                'items',
                'delivery_date',
                'delivery_time',
                'remarks',
            ]);
        });
    }
};
