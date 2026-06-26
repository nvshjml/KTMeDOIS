<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\PurchaseOrder;

class SupplierDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample vendors in supplier database
        Vendor::on('supplier')->create([
            'vendor_ID' => 1,
            'vendor_name' => 'Vendor A Sdn Bhd',
            'billing_address' => '123, Jalan Vendor, 50400 Kuala Lumpur',
            'vendor_number' => 1001,
            'contact_person' => 'Ahmad Bin Abdullah',
            'phone' => '0123456789',
            'email' => 'vendorA@example.com',
            'status' => 'active',
        ]);

        Vendor::on('supplier')->create([
            'vendor_ID' => 2,
            'vendor_name' => 'Vendor B Sdn Bhd',
            'billing_address' => '456, Jalan Vendor B, 50300 Kuala Lumpur',
            'vendor_number' => 1002,
            'contact_person' => 'Zainal Bin Abidin',
            'phone' => '0187654321',
            'email' => 'vendorB@example.com',
            'status' => 'active',
        ]);

        // Create sample purchase orders in supplier database
        PurchaseOrder::on('supplier')->create([
            'PO_ID' => 1,
            'vendor_ID' => 1,
            'project_reference' => 'PROJ-2024-001',
            'issue_date' => '2024-01-15',
            'total_amount' => 50000.00,
            'status' => 'open',
        ]);

        PurchaseOrder::on('supplier')->create([
            'PO_ID' => 2,
            'vendor_ID' => 2,
            'project_reference' => 'PROJ-2024-002',
            'issue_date' => '2024-02-10',
            'total_amount' => 75000.00,
            'status' => 'open',
        ]);
    }
}
