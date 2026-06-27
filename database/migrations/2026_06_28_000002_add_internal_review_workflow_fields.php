<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table): void {
            $table->foreignId('assigned_reviewer_id')
                ->nullable()
                ->after('cust_id')
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->foreignId('assigned_by_id')
                ->nullable()
                ->after('assigned_reviewer_id')
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->timestamp('forwarded_at')->nullable()->after('assigned_by_id');
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->foreignId('assigned_finance_id')
                ->nullable()
                ->after('cust_id')
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->foreignId('assigned_by_id')
                ->nullable()
                ->after('assigned_finance_id')
                ->constrained('customers', 'cust_id')
                ->nullOnDelete();
            $table->timestamp('forwarded_at')->nullable()->after('assigned_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('assigned_finance_id');
            $table->dropConstrainedForeignId('assigned_by_id');
            $table->dropColumn('forwarded_at');
        });

        Schema::table('delivery_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('assigned_reviewer_id');
            $table->dropConstrainedForeignId('assigned_by_id');
            $table->dropColumn('forwarded_at');
        });
    }
};
