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
            $table->foreignId('supplier_id')->constrained('suppliers', 'supplier_id');
            $table->string('do_number');
            $table->string('po_number');
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
