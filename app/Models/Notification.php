<?php

namespace App\Models;

use App\Mail\SystemNotificationMail;
use App\Models\Concerns\UsesMainDatabaseConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Throwable;

class Notification extends Model
{
    use UsesMainDatabaseConnection;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'supplier_id',
        'cust_id',
        'type',
        'content',
        'status',
    ];

    protected static function booted(): void
    {
        static::created(function (Notification $notification): void {
            if (app()->runningInConsole() && ! app()->runningUnitTests()) {
                return;
            }

            $notification->sendEmailCopy();
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'SUPPLIERID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }

    public function sendEmailCopy(): void
    {
        $recipient = $this->emailRecipient();

        if (! $recipient) {
            return;
        }

        try {
            Mail::to($recipient)->send(new SystemNotificationMail($this->emailSubject(), $this->content));
        } catch (Throwable) {
            report(new \RuntimeException('Unable to send KTM eDOIS notification email to '.$recipient));
        }
    }

    private function emailRecipient(): ?string
    {
        if ($this->cust_id) {
            return $this->customer()->value('user_email');
        }

        if ($this->supplier_id) {
            return $this->supplier()->value('SUPPLIER_EMAIL_ADD');
        }

        return null;
    }

    private function emailSubject(): string
    {
        return $this->supplier_id
            ? 'KTM eDOIS Status Update'
            : 'KTM eDOIS Notification';
    }
}
