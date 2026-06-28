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
            ['user_email' => env('SEED_ADMIN_EMAIL', env('SEED_CUSTOMER_EMAIL', 'ktmedois.admin@gmail.com'))],
            [
                'username' => 'admin',
                'display_name' => 'KTM Admin',
                'password_hash' => Hash::make('password123'),
                'user_role' => 'admin',
                'user_status' => 'active',
            ]
        );
        $reviewer = Customer::updateOrCreate(
            ['user_email' => env('SEED_REVIEWER_EMAIL', 'ktmedois.reviewer@gmail.com')],
            [
                'username' => 'reviewer',
                'display_name' => 'KTM Reviewer',
                'password_hash' => Hash::make('password123'),
                'user_role' => 'reviewer',
                'user_status' => 'active',
            ]
        );
        $finance = Customer::updateOrCreate(
            ['user_email' => env('SEED_FINANCE_EMAIL', 'ktmedois.finance@gmail.com')],
            [
                'username' => 'finance',
                'display_name' => 'KTM Finance',
                'password_hash' => Hash::make('password123'),
                'user_role' => 'finance',
                'user_status' => 'active',
            ]
        );

        $this->call(SupplierDatabaseSeeder::class);

        Storage::disk('local')->put('delivery-orders/sample-do-v001.pdf', 'Sample Delivery Order document for V001.');
        Storage::disk('local')->put('delivery-orders/sample-proof-v001.pdf', 'Sample proof of delivery for V001.');
        Storage::disk('local')->put('delivery-orders/sample-do-v002.pdf', 'Sample Delivery Order document for V002.');
        Storage::disk('local')->put('delivery-orders/sample-proof-v002.pdf', 'Sample proof of delivery for V002.');
        Storage::disk('local')->put('delivery-orders/sample-do-v004.pdf', 'Sample Delivery Order document for V004.');
        Storage::disk('local')->put('delivery-orders/sample-proof-v004.pdf', 'Sample proof of delivery for V004.');
        Storage::disk('local')->put('delivery-orders/sample-do-v005.pdf', 'Sample Delivery Order document for V005.');
        Storage::disk('local')->put('delivery-orders/sample-proof-v005.pdf', 'Sample proof of delivery for V005.');

        $supplierOne = Supplier::where('SUPPLIERID', 'V001')->firstOrFail();
        $supplierTwo = Supplier::where('SUPPLIERID', 'V002')->firstOrFail();
        $supplierFour = Supplier::where('SUPPLIERID', 'V004')->firstOrFail();
        $supplierFive = Supplier::where('SUPPLIERID', 'V005')->firstOrFail();

        $approvedDo = DeliveryOrder::create([
            'supplier_id' => $supplierOne->supplier_id,
            'cust_id' => $customer->cust_id,
            'do_number' => 'DO-V001-1001',
            'po_number' => 'PO-KTM-2026-001',
            'do_link' => 'delivery-orders/sample-do-v001.pdf',
            'proof_link' => 'delivery-orders/sample-proof-v001.pdf',
            'status' => 'Approved',
            'created_date' => now()->subDays(5),
        ]);

        $submittedDo = DeliveryOrder::create([
            'supplier_id' => $supplierTwo->supplier_id,
            'cust_id' => $customer->cust_id,
            'assigned_reviewer_id' => $reviewer->cust_id,
            'assigned_by_id' => $customer->cust_id,
            'forwarded_at' => now()->subHours(10),
            'do_number' => 'DO-V002-1002',
            'po_number' => 'PO-KTM-2026-002',
            'do_link' => 'delivery-orders/sample-do-v002.pdf',
            'proof_link' => 'delivery-orders/sample-proof-v002.pdf',
            'status' => 'Under Review',
            'created_date' => now()->subDay(),
        ]);

        $pendingDo = DeliveryOrder::create([
            'supplier_id' => $supplierFour->supplier_id,
            'cust_id' => $customer->cust_id,
            'do_number' => 'DO-V004-1003',
            'po_number' => 'PO-KTM-2026-003',
            'do_link' => 'delivery-orders/sample-do-v004.pdf',
            'proof_link' => 'delivery-orders/sample-proof-v004.pdf',
            'status' => 'Submitted',
            'created_date' => now()->subHours(8),
        ]);

        $rejectedDo = DeliveryOrder::create([
            'supplier_id' => $supplierFive->supplier_id,
            'cust_id' => $customer->cust_id,
            'assigned_reviewer_id' => $reviewer->cust_id,
            'assigned_by_id' => $customer->cust_id,
            'forwarded_at' => now()->subDays(2),
            'do_number' => 'DO-V005-1004',
            'po_number' => 'PO-KTM-2026-004',
            'do_link' => 'delivery-orders/sample-do-v005.pdf',
            'proof_link' => 'delivery-orders/sample-proof-v005.pdf',
            'status' => 'Rejected',
            'reason' => 'Proof of delivery is incomplete. Receiver stamp is missing.',
            'created_date' => now()->subDays(3),
        ]);

        $invoice = Invoice::create([
            'do_id' => $approvedDo->do_id,
            'cust_id' => $customer->cust_id,
            'assigned_finance_id' => $finance->cust_id,
            'assigned_by_id' => $customer->cust_id,
            'forwarded_at' => now()->subHours(6),
            'invoice_number' => 'INV-V001-9001',
            'description' => 'Rail component delivery for approved Delivery Order.',
            'issue_date' => now()->subDays(3)->toDateString(),
            'subtotal' => 12000,
            'tax' => 720,
            'credit_note' => 200,
            'total' => 12520,
            'status' => 'Finance Review',
        ]);

        $processingInvoice = Invoice::create([
            'do_id' => $approvedDo->do_id,
            'cust_id' => $customer->cust_id,
            'assigned_finance_id' => $finance->cust_id,
            'assigned_by_id' => $customer->cust_id,
            'forwarded_at' => now()->subDays(2),
            'invoice_number' => 'INV-V001-9002',
            'description' => 'Additional track material delivery under approved DO.',
            'issue_date' => now()->subDays(2)->toDateString(),
            'subtotal' => 8200,
            'tax' => 492,
            'credit_note' => 0,
            'penalty' => 82,
            'total' => 8610,
            'status' => 'Payment Processing',
        ]);

        $paidInvoice = Invoice::create([
            'do_id' => $approvedDo->do_id,
            'cust_id' => $customer->cust_id,
            'assigned_finance_id' => $finance->cust_id,
            'assigned_by_id' => $customer->cust_id,
            'forwarded_at' => now()->subDays(6),
            'invoice_number' => 'INV-V001-9003',
            'description' => 'Completed rail hardware claim.',
            'issue_date' => now()->subDays(7)->toDateString(),
            'subtotal' => 5400,
            'tax' => 324,
            'credit_note' => 100,
            'penalty' => 0,
            'total' => 5624,
            'status' => 'Paid',
        ]);

        Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => 'do_submitted',
            'content' => $supplierTwo->supplier_name.' submitted Delivery Order '.$submittedDo->do_number.'.',
            'status' => 'unread',
        ]);

        Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => 'do_submitted',
            'content' => $supplierFour->supplier_name.' submitted Delivery Order '.$pendingDo->do_number.'.',
            'status' => 'unread',
        ]);

        Notification::create([
            'cust_id' => $reviewer->cust_id,
            'type' => 'do_assigned',
            'content' => 'Delivery Order '.$submittedDo->do_number.' has been assigned to you for review.',
            'status' => 'unread',
        ]);

        Notification::create([
            'supplier_id' => $supplierOne->supplier_id,
            'type' => 'do_approved',
            'content' => 'Delivery Order '.$approvedDo->do_number.' has been approved.',
            'status' => 'unread',
        ]);

        Notification::create([
            'supplier_id' => $supplierFive->supplier_id,
            'type' => 'do_rejected',
            'content' => 'Delivery Order '.$rejectedDo->do_number.' was rejected: '.$rejectedDo->reason,
            'status' => 'unread',
        ]);

        Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => 'invoice_submitted',
            'content' => $supplierOne->supplier_name.' submitted Invoice '.$invoice->invoice_number.'.',
            'status' => 'unread',
        ]);

        Notification::create([
            'cust_id' => $finance->cust_id,
            'type' => 'invoice_assigned',
            'content' => 'Invoice '.$invoice->invoice_number.' has been assigned to you for finance review.',
            'status' => 'unread',
        ]);

        Notification::create([
            'supplier_id' => $supplierOne->supplier_id,
            'type' => 'invoice_payment_processing',
            'content' => 'Invoice '.$processingInvoice->invoice_number.' moved to Payment Processing.',
            'status' => 'unread',
        ]);

        Notification::create([
            'supplier_id' => $supplierOne->supplier_id,
            'type' => 'invoice_paid',
            'content' => 'Invoice '.$paidInvoice->invoice_number.' has been marked as Paid.',
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
            'supplier_id' => $supplierFour->supplier_id,
            'action' => 'DO submission',
            'affected_record' => 'delivery_orders:'.$pendingDo->do_id,
            'timestamp' => now()->subHours(8),
        ]);

        AuditLog::create([
            'cust_id' => $customer->cust_id,
            'supplier_id' => $supplierTwo->supplier_id,
            'action' => 'DO reviewer assignment',
            'affected_record' => 'delivery_orders:'.$submittedDo->do_id.':reviewer:'.$reviewer->cust_id,
            'timestamp' => now()->subHours(10),
        ]);

        AuditLog::create([
            'cust_id' => $reviewer->cust_id,
            'supplier_id' => $supplierFive->supplier_id,
            'action' => 'DO rejection',
            'affected_record' => 'delivery_orders:'.$rejectedDo->do_id,
            'timestamp' => now()->subDays(2),
        ]);

        AuditLog::create([
            'supplier_id' => $supplierOne->supplier_id,
            'action' => 'invoice submission',
            'affected_record' => 'invoices:'.$invoice->invoice_id,
            'timestamp' => now()->subDays(3),
        ]);

        AuditLog::create([
            'cust_id' => $customer->cust_id,
            'supplier_id' => $supplierOne->supplier_id,
            'action' => 'invoice finance assignment',
            'affected_record' => 'invoices:'.$invoice->invoice_id.':finance:'.$finance->cust_id,
            'timestamp' => now()->subHours(6),
        ]);

        AuditLog::create([
            'cust_id' => $finance->cust_id,
            'supplier_id' => $supplierOne->supplier_id,
            'action' => 'invoice payment processing',
            'affected_record' => 'invoices:'.$processingInvoice->invoice_id,
            'timestamp' => now()->subDays(2),
        ]);

        AuditLog::create([
            'cust_id' => $finance->cust_id,
            'supplier_id' => $supplierOne->supplier_id,
            'action' => 'invoice paid',
            'affected_record' => 'invoices:'.$paidInvoice->invoice_id,
            'timestamp' => now()->subDays(5),
        ]);
    }
}
