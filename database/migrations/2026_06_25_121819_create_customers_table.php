<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('cust_id');
            $table->string('username')->unique();
            $table->string('display_name')->nullable();
            $table->string('password_hash');
            $table->string('user_role')->default('admin');
            $table->string('user_email')->unique();
            $table->enum('user_status', ['active', 'inactive'])->default('active');
            $table->dateTime('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
