<?php

namespace Tests\Feature;

use App\Mail\CustomerPasswordResetMail;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KtmedoisFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_supplier_demo_flow(): void
    {
        Storage::fake('local');

        $customer = Customer::create([
            'username' => 'customer',
            'password_hash' => Hash::make('password123'),
            'user_role' => 'customer',
            'user_email' => 'customer@ktm.test',
            'user_status' => 'active',
        ]);

        $supplier = Supplier::create([
            'supplier_name' => 'KTM Track Materials Sdn Bhd',
            'billing_address' => 'Cyberjaya',
            'vendor_number' => 'V001',
            'contact_person' => 'Ahmad Faris',
            'supplier_phone' => '03-8800 1001',
            'supplier_email' => 'supplier1@test.com',
            'supplier_status' => 'active',
        ]);

        $this->post('/supplier/verify', [
            'vendor_number' => 'V001',
            'supplier_email' => 'supplier1@test.com',
        ])->assertRedirect(route('supplier.profile'));

        $this->post('/supplier/delivery-orders', [
            'do_number' => 'DO-TEST-001',
            'po_number' => 'PO-TEST-001',
            'do_file' => UploadedFile::fake()->create('do.pdf', 20, 'application/pdf'),
            'proof_file' => UploadedFile::fake()->create('proof.png', 20, 'image/png'),
        ])->assertRedirect(route('supplier.do.status'));

        $deliveryOrder = DeliveryOrder::where('do_number', 'DO-TEST-001')->firstOrFail();
        $this->assertSame('Submitted', $deliveryOrder->status);

        $this->post('/login', [
            'login' => 'customer',
            'password' => 'password123',
            'login_as' => 'customer',
        ])->assertRedirect(route('customer.dashboard'));

        $this->actingAs($customer)
            ->post(route('customer.delivery-orders.approve', $deliveryOrder->do_id))
            ->assertSessionHas('success');

        $this->assertSame('Approved', $deliveryOrder->refresh()->status);

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->post('/supplier/invoices', [
                'do_id' => $deliveryOrder->do_id,
                'invoice_number' => 'INV-TEST-001',
                'description' => 'Demo invoice',
                'issue_date' => now()->toDateString(),
                'subtotal' => 100,
                'tax' => 6,
                'credit_note' => 1,
                'apply_penalty' => 1,
            ])->assertRedirect(route('supplier.invoice.status'));

        $invoice = Invoice::where('invoice_number', 'INV-TEST-001')->firstOrFail();
        $this->assertSame('6.00', (string) $invoice->tax);
        $this->assertSame('1.00', (string) $invoice->credit_note);
        $this->assertSame('1.00', (string) $invoice->penalty);
        $this->assertSame('104.00', (string) $invoice->total);

        $this->actingAs($customer)
            ->post(route('customer.invoices.payment-processing', $invoice->invoice_id))
            ->assertSessionHas('success');

        $this->actingAs($customer)
            ->post(route('customer.invoices.paid', $invoice->invoice_id))
            ->assertSessionHas('success');

        $this->assertSame('Paid', $invoice->refresh()->status);
        $this->assertGreaterThanOrEqual(4, Notification::count());
        $this->assertTrue(AuditLog::where('action', 'invoice paid')->exists());
    }

    public function test_supplier_can_verify_from_shared_login(): void
    {
        $supplier = Supplier::create([
            'supplier_name' => 'KTM Track Materials Sdn Bhd',
            'billing_address' => 'Cyberjaya',
            'vendor_number' => 'V001',
            'contact_person' => 'Ahmad Faris',
            'supplier_phone' => '03-8800 1001',
            'supplier_email' => 'supplier1@test.com',
            'password_hash' => Hash::make('password123'),
            'supplier_status' => 'active',
        ]);

        $this->post('/login', [
            'login_as' => 'supplier',
            'login' => 'V001',
            'password' => 'password123',
        ])->assertRedirect(route('supplier.profile'))
            ->assertSessionHas('supplier_id', $supplier->supplier_id);

        $this->assertGuest();
        $this->assertTrue(
            AuditLog::where('action', 'supplier validation')
                ->where('supplier_id', $supplier->supplier_id)
                ->exists()
        );
    }

    public function test_inactive_supplier_can_login_but_cannot_upload_delivery_order(): void
    {
        Storage::fake('local');

        $supplier = Supplier::create([
            'supplier_name' => 'Inactive Signal Works Sdn Bhd',
            'billing_address' => 'Johor Bahru',
            'vendor_number' => 'V003',
            'contact_person' => 'Kumar Raj',
            'supplier_phone' => '07-550 1003',
            'supplier_email' => 'supplier3@test.com',
            'password_hash' => Hash::make('password123'),
            'supplier_status' => 'inactive',
            'inactive_date' => now()->subMonth(),
        ]);

        $this->post('/login', [
            'login_as' => 'supplier',
            'login' => 'V003',
            'password' => 'password123',
        ])->assertRedirect(route('supplier.profile'))
            ->assertSessionHas('supplier_id', $supplier->supplier_id)
            ->assertSessionHas('warning');

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->get(route('supplier.do.status'))
            ->assertOk()
            ->assertSee('Upload Disabled');

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->post('/supplier/delivery-orders', [
                'do_number' => 'DO-INACTIVE-001',
                'po_number' => 'PO-INACTIVE-001',
                'do_file' => UploadedFile::fake()->create('do.pdf', 20, 'application/pdf'),
                'proof_file' => UploadedFile::fake()->create('proof.png', 20, 'image/png'),
            ])->assertRedirect(route('supplier.do.status'))
            ->assertSessionHas('error', 'This supplier is inactive and cannot upload Delivery Orders.');

        $this->assertFalse(DeliveryOrder::where('do_number', 'DO-INACTIVE-001')->exists());
    }

    public function test_customer_password_reset_sends_mail_and_updates_password(): void
    {
        Mail::fake();

        $customer = Customer::create([
            'username' => 'customer',
            'password_hash' => Hash::make('password123'),
            'user_role' => 'customer',
            'user_email' => 'customer@ktm.test',
            'user_status' => 'active',
        ]);

        $this->post(route('password.email'), [
            'user_email' => 'customer@ktm.test',
        ])->assertSessionHas('success')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'customer@ktm.test',
        ]);

        $resetUrl = null;

        Mail::assertSent(CustomerPasswordResetMail::class, function (CustomerPasswordResetMail $mail) use ($customer, &$resetUrl) {
            $resetUrl = $mail->resetUrl;

            return $mail->hasTo('customer@ktm.test') && $mail->customer->is($customer);
        });

        $resetPath = parse_url($resetUrl, PHP_URL_PATH);
        $resetQuery = parse_url($resetUrl, PHP_URL_QUERY);
        parse_str((string) $resetQuery, $query);

        $this->actingAs($customer)
            ->get($resetPath.'?'.$resetQuery)
            ->assertOk()
            ->assertSee('Reset Password')
            ->assertSee('customer@ktm.test');

        $this->post(route('password.update'), [
            'email' => $query['email'],
            'token' => basename((string) $resetPath),
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertGuest();
        $this->assertTrue(Hash::check('newpassword123', $customer->refresh()->password_hash));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'customer@ktm.test',
        ]);

        $this->post('/login', [
            'login' => 'customer',
            'password' => 'newpassword123',
            'login_as' => 'customer',
        ])->assertRedirect(route('customer.dashboard'));
    }

    public function test_key_ui_pages_render_with_ktm_dashboard_design(): void
    {
        $customer = Customer::create([
            'username' => 'customer',
            'password_hash' => Hash::make('password123'),
            'user_role' => 'customer',
            'user_email' => 'customer@ktm.test',
            'user_status' => 'active',
        ]);

        $supplier = Supplier::create([
            'supplier_name' => 'KTM Track Materials Sdn Bhd',
            'billing_address' => 'Cyberjaya',
            'vendor_number' => 'V001',
            'contact_person' => 'Ahmad Faris',
            'supplier_phone' => '03-8800 1001',
            'supplier_email' => 'supplier1@test.com',
            'supplier_status' => 'active',
        ]);

        $deliveryOrder = DeliveryOrder::create([
            'supplier_id' => $supplier->supplier_id,
            'do_number' => 'DO-APPROVED-001',
            'po_number' => 'PO-APPROVED-001',
            'do_link' => 'delivery-orders/do.pdf',
            'proof_link' => 'delivery-orders/proof.pdf',
            'status' => 'Approved',
            'created_date' => now(),
        ]);

        $invoice = Invoice::create([
            'do_id' => $deliveryOrder->do_id,
            'cust_id' => $customer->cust_id,
            'invoice_number' => 'INV-PRINT-001',
            'description' => 'Printable invoice',
            'issue_date' => now()->toDateString(),
            'subtotal' => 100,
            'tax' => 6,
            'credit_note' => 1,
            'penalty' => 1,
            'total' => 104,
            'status' => 'Submitted',
        ]);

        $this->actingAs($customer)
            ->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee('KTM eDOIS Dashboard')
            ->assertSee('Vendor Registry Integration');

        $this->actingAs($customer)
            ->get(route('customer.delivery-orders.print', $deliveryOrder->do_id))
            ->assertOk()
            ->assertSee('DELIVERY ORDER')
            ->assertSee('Print / Save PDF');

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->get(route('supplier.do.create'))
            ->assertOk()
            ->assertSee('Submit Delivery Order')
            ->assertSee('Delivery Items');

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->get(route('supplier.do.print', $deliveryOrder->do_id))
            ->assertOk()
            ->assertSee('DELIVERY ORDER');

        $this->actingAs($customer)
            ->get(route('customer.invoices.print', $invoice->invoice_id))
            ->assertOk()
            ->assertSee('INVOICE')
            ->assertSee('Total Claim');

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->get(route('supplier.invoice.print', $invoice->invoice_id))
            ->assertOk()
            ->assertSee('INVOICE');

        $this->withSession(['supplier_id' => $supplier->supplier_id])
            ->get(route('supplier.invoice.create', $deliveryOrder->do_id))
            ->assertOk()
            ->assertSee('Invoice Creation')
            ->assertSee('Balance Due Preview');
    }
}
