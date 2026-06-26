<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- CUSTOMER ACCOUNTS ---
        
        // Customer 1 (YTL Cement)
        User::factory()->create([
            'name' => 'YTL Cement Berhad',
            'username' => 'customer1',
            'email' => 'ytl_cement@ktm.com',
            'password' => Hash::make('1234'),
            'role' => 'customer',
        ]);

        // Customer 2 (Kontena Nasional)
        User::factory()->create([
            'name' => 'Kontena Nasional Berhad',
            'username' => 'customer2',
            'email' => 'kontena_nasional@ktm.com',
            'password' => Hash::make('1234'),
            'role' => 'customer',
        ]);


        // --- SUPPLIER ACCOUNTS ---
        
        // Supplier 1 (Majestic Engineering)
        User::factory()->create([
            'name' => 'Majestic Engineering Sdn Bhd',
            'username' => 'supplier1',
            'email' => 'majestic_eng@ktm.com',
            'password' => Hash::make('1234'),
            'role' => 'supplier',
        ]);

        // Supplier 2 (CMC Engineering)
        User::factory()->create([
            'name' => 'CMC Engineering Sdn Bhd',
            'username' => 'supplier2',
            'email' => 'cmc_engineering@ktm.com',
            'password' => Hash::make('1234'),
            'role' => 'supplier',
        ]);
    }
}