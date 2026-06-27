<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Supplier;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    public function forCustomer(Customer $customer, string $type, string $content): Notification
    {
        $notification = Notification::create([
            'cust_id' => $customer->cust_id,
            'type' => $type,
            'content' => $content,
            'status' => 'unread',
        ]);

        $this->sendEmail($customer->user_email, 'KTM eDOIS Notification', $content);

        return $notification;
    }

    public function forAllCustomers(string $type, string $content): void
    {
        Customer::where('user_status', 'active')->each(function (Customer $customer) use ($type, $content): void {
            $this->forCustomer($customer, $type, $content);
        });
    }

    public function forSupplier(Supplier $supplier, string $type, string $content): Notification
    {
        $notification = Notification::create([
            'supplier_id' => $supplier->supplier_id,
            'type' => $type,
            'content' => $content,
            'status' => 'unread',
        ]);

        $this->sendEmail($supplier->supplier_email, 'KTM eDOIS Status Update', $content);

        return $notification;
    }

    private function sendEmail(?string $email, string $subject, string $content): void
    {
        if (! $email) {
            return;
        }

        try {
            Mail::raw($content, function ($message) use ($email, $subject): void {
                $message->to($email)->subject($subject);
            });
        } catch (Throwable) {
            report(new \RuntimeException('Unable to send KTM eDOIS notification email to '.$email));
        }
    }
}
