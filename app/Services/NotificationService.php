<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Supplier;

class NotificationService
{
    public function forCustomer(Customer $customer, string $type, string $content): Notification
    {
        return Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => $type,
            'content' => $content,
            'status' => 'unread',
        ]);
    }

    public function forAllCustomers(string $type, string $content): void
    {
        Customer::where('user_status', 'active')->each(function (Customer $customer) use ($type, $content): void {
            $this->forCustomer($customer, $type, $content);
        });
    }

    public function forSupplier(Supplier $supplier, string $type, string $content): Notification
    {
        return Notification::create([
            'supplier_id' => $supplier->supplier_id,
            'type' => $type,
            'content' => $content,
            'status' => 'unread',
        ]);
    }
}
