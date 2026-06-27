<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createSupplierDatabaseIfNeeded();

        Schema::connection($this->supplierConnection())->dropIfExists('supplier_details');

        Schema::connection($this->supplierConnection())->create('supplier_details', function (Blueprint $table) {
            $table->string('SUPPLIERID', 25)->primary();
            $table->string('SUPPLIER_COMP_REG_NO', 200)->nullable();
            $table->string('SUPPLIER_COMP_NAME', 200)->nullable();
            $table->string('SUPPLIER_CTC_NO', 200)->nullable();
            $table->string('SUPPLIER_CTC_PERSON', 100)->nullable();
            $table->string('SUPPLIER_EMAIL_ADD', 200)->nullable()->unique();
            $table->date('SUPPLIER_EXPIRED_DATE')->nullable();
            $table->string('SUPPLIER_CTC_STATUS', 20)->nullable()->index();
            $table->string('password_hash')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->supplierConnection())->dropIfExists('supplier_details');
    }

    private function createSupplierDatabaseIfNeeded(): void
    {
        if (app()->environment('testing') || config('database.connections.supplier.driver') !== 'mysql') {
            return;
        }

        $database = config('database.connections.supplier.database');
        $charset = config('database.connections.supplier.charset', 'utf8mb4');
        $collation = config('database.connections.supplier.collation', 'utf8mb4_unicode_ci');

        DB::connection('mysql')->statement(
            sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s',
                str_replace('`', '``', $database),
                $charset,
                $collation
            )
        );
    }

    private function supplierConnection(): string
    {
        return app()->environment('testing') ? config('database.default') : 'supplier';
    }
};
