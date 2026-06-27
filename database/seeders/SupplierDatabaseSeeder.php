<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::create([
            'supplier_name' => 'KTM Track Materials Sdn Bhd',
            'billing_address' => 'No. 12, Jalan Teknologi, 63000 Cyberjaya, Selangor',
            'vendor_number' => 'V001',
            'contact_person' => 'Ahmad Faris',
            'supplier_phone' => '03-8800 1001',
            'supplier_email' => 'supplier1@test.com',
            'supplier_status' => 'active',
        ]);

        Supplier::create([
            'supplier_name' => 'Rail Parts Services Sdn Bhd',
            'billing_address' => 'Lot 55, Jalan Perusahaan, 41000 Klang, Selangor',
            'vendor_number' => 'V002',
            'contact_person' => 'Siti Zaleha',
            'supplier_phone' => '03-3320 1002',
            'supplier_email' => 'supplier2@test.com',
            'supplier_status' => 'active',
        ]);

        Supplier::create([
            'supplier_name' => 'Inactive Signal Works Sdn Bhd',
            'billing_address' => 'Block C, Jalan Industri, 81200 Johor Bahru, Johor',
            'vendor_number' => 'V003',
            'contact_person' => 'Kumar Raj',
            'supplier_phone' => '07-550 1003',
            'supplier_email' => 'supplier3@test.com',
            'supplier_status' => 'inactive',
            'inactive_date' => now()->subMonth(),
        ]);
    }
}
