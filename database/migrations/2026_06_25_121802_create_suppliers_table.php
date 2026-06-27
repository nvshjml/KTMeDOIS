<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supplier_id');
            $table->string('supplier_name');
            $table->text('billing_address')->nullable();
            $table->string('vendor_number')->unique();
            $table->string('contact_person')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->string('supplier_email')->unique();
            $table->enum('supplier_status', ['active', 'inactive'])->default('active');
            $table->dateTime('inactive_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
