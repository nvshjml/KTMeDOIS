<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupplierDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createSupplierWithAccount([
            'supplier_name' => 'KTM Track Materials Sdn Bhd',
            'billing_address' => 'No. 12, Jalan Teknologi, 63000 Cyberjaya, Selangor',
            'vendor_number' => 'V001',
            'contact_person' => 'Ahmad Faris',
            'supplier_phone' => '03-8800 1001',
            'supplier_email' => env('SEED_SUPPLIER_ONE_EMAIL', 'ktmedois.supplier1@gmail.com'),
            'supplier_status' => 'active',
        ]);

        $this->createSupplierWithAccount([
            'supplier_name' => 'Rail Parts Services Sdn Bhd',
            'billing_address' => 'Lot 55, Jalan Perusahaan, 41000 Klang, Selangor',
            'vendor_number' => 'V002',
            'contact_person' => 'Siti Zaleha',
            'supplier_phone' => '03-3320 1002',
            'supplier_email' => env('SEED_SUPPLIER_TWO_EMAIL', 'ktmedois.supplier2@gmail.com'),
            'supplier_status' => 'active',
        ]);

        $this->createSupplierWithAccount([
            'supplier_name' => 'Metro Rail Engineering Sdn Bhd',
            'billing_address' => 'No. 7, Jalan Ampang Hilir, 55000 Kuala Lumpur',
            'vendor_number' => 'V004',
            'contact_person' => 'Nur Aina',
            'supplier_phone' => '03-4250 1004',
            'supplier_email' => env('SEED_SUPPLIER_FOUR_EMAIL', 'ktmedois.supplier4@gmail.com'),
            'supplier_status' => 'active',
        ]);

        $this->createSupplierWithAccount([
            'supplier_name' => 'Pantai Signal Systems Sdn Bhd',
            'billing_address' => 'Lot 18, Kawasan Perindustrian Prai, 13600 Perai, Pulau Pinang',
            'vendor_number' => 'V005',
            'contact_person' => 'Daniel Tan',
            'supplier_phone' => '04-390 1005',
            'supplier_email' => env('SEED_SUPPLIER_FIVE_EMAIL', 'ktmedois.supplier5@gmail.com'),
            'supplier_status' => 'active',
        ]);

        $this->createSupplierWithAccount([
            'supplier_name' => 'Inactive Signal Works Sdn Bhd',
            'billing_address' => 'Block C, Jalan Industri, 81200 Johor Bahru, Johor',
            'vendor_number' => 'V003',
            'contact_person' => 'Kumar Raj',
            'supplier_phone' => '07-550 1003',
            'supplier_email' => env('SEED_SUPPLIER_THREE_EMAIL', 'ktmedois.supplier3@gmail.com'),
            'supplier_status' => 'inactive',
            'inactive_date' => now()->subMonth(),
        ]);
    }

    private function createSupplierWithAccount(array $attributes): void
    {
        Supplier::updateOrCreate(
            ['SUPPLIERID' => $attributes['vendor_number']],
            [
                'SUPPLIER_COMP_REG_NO' => $attributes['company_registration_no'] ?? null,
                'SUPPLIER_COMP_NAME' => $attributes['supplier_name'],
                'SUPPLIER_CTC_NO' => $attributes['supplier_phone'] ?? null,
                'SUPPLIER_CTC_PERSON' => $attributes['contact_person'] ?? null,
                'SUPPLIER_EMAIL_ADD' => $attributes['supplier_email'] ?? null,
                'SUPPLIER_EXPIRED_DATE' => $attributes['inactive_date'] ?? null,
                'SUPPLIER_CTC_STATUS' => $attributes['supplier_status'] ?? 'active',
                'password_hash' => Hash::make('password123'),
            ]
        );
    }
}
