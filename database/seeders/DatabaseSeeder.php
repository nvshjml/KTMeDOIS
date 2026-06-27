<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::updateOrCreate(
            ['user_email' => 'customer@ktm.test'],
            [
                'username' => 'customer',
                'display_name' => 'KTM Customer',
                'password_hash' => Hash::make('password123'),
                'user_role' => 'customer',
                'user_status' => 'active',
            ]
        );

        $this->call(SupplierDatabaseSeeder::class);

        Storage::disk('local')->put('delivery-orders/sample-do-v001.pdf', 'Sample Delivery Order document for V001.');
        Storage::disk('local')->put('delivery-orders/sample-proof-v001.pdf', 'Sample proof of delivery for V001.');
        Storage::disk('local')->put('delivery-orders/sample-do-v002.pdf', 'Sample Delivery Order document for V002.');
        Storage::disk('local')->put('delivery-orders/sample-proof-v002.pdf', 'Sample proof of delivery for V002.');

        $supplierOne = Supplier::where('SUPPLIERID', 'V001')->firstOrFail();
        $supplierTwo = Supplier::where('SUPPLIERID', 'V002')->firstOrFail();

        $approvedDo = DeliveryOrder::create([
            'supplier_id' => $supplierOne->supplier_id,
            'do_number' => 'DO-V001-1001',
            'po_number' => 'PO-KTM-2026-001',
            'do_link' => 'delivery-orders/sample-do-v001.pdf',
            'proof_link' => 'delivery-orders/sample-proof-v001.pdf',
            'status' => 'Approved',
            'created_date' => now()->subDays(5),
        ]);

        $submittedDo = DeliveryOrder::create([
            'supplier_id' => $supplierTwo->supplier_id,
            'do_number' => 'DO-V002-1002',
            'po_number' => 'PO-KTM-2026-002',
            'do_link' => 'delivery-orders/sample-do-v002.pdf',
            'proof_link' => 'delivery-orders/sample-proof-v002.pdf',
            'status' => 'Submitted',
            'created_date' => now()->subDay(),
        ]);

        $invoice = Invoice::create([
            'do_id' => $approvedDo->do_id,
            'cust_id' => $customer->cust_id,
            'invoice_number' => 'INV-V001-9001',
            'description' => 'Rail component delivery for approved Delivery Order.',
            'issue_date' => now()->subDays(3)->toDateString(),
            'subtotal' => 12000,
            'tax' => 720,
            'credit_note' => 200,
            'total' => 12520,
            'status' => 'Submitted',
        ]);

        Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => 'do_submitted',
            'content' => $supplierTwo->supplier_name.' submitted Delivery Order '.$submittedDo->do_number.'.',
            'status' => 'unread',
        ]);

        Notification::create([
            'supplier_id' => $supplierOne->supplier_id,
            'type' => 'do_approved',
            'content' => 'Delivery Order '.$approvedDo->do_number.' has been approved.',
            'status' => 'unread',
        ]);

        Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => 'invoice_submitted',
            'content' => $supplierOne->supplier_name.' submitted Invoice '.$invoice->invoice_number.'.',
            'status' => 'unread',
        ]);

        AuditLog::create([
            'cust_id' => $customer->cust_id,
            'supplier_id' => $supplierOne->supplier_id,
            'action' => 'DO approval',
            'affected_record' => 'delivery_orders:'.$approvedDo->do_id,
            'timestamp' => now()->subDays(4),
        ]);

        AuditLog::create([
            'supplier_id' => $supplierTwo->supplier_id,
            'action' => 'DO submission',
            'affected_record' => 'delivery_orders:'.$submittedDo->do_id,
            'timestamp' => now()->subDay(),
        ]);

        AuditLog::create([
            'supplier_id' => $supplierOne->supplier_id,
            'action' => 'invoice submission',
            'affected_record' => 'invoices:'.$invoice->invoice_id,
            'timestamp' => now()->subDays(3),
        ]);
    }
}
