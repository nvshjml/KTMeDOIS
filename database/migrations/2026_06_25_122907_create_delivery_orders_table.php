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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id('do_id');
            $table->string('supplier_id', 25)->index();
            $table->foreignId('cust_id')
                ->nullable()
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->foreignId('assigned_reviewer_id')
                ->nullable()
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->foreignId('assigned_by_id')
                ->nullable()
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->timestamp('forwarded_at')->nullable();
            $table->string('do_number');
            $table->string('po_number');
            $table->date('order_date')->nullable();
            $table->string('invoice_reference')->nullable();
            $table->string('project_reference')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('invoice_address')->nullable();
            $table->json('items')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->text('remarks')->nullable();
            $table->string('do_link');
            $table->string('proof_link');
            $table->string('status')->default('Submitted');
            $table->text('reason')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
