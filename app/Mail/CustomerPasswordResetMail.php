<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public string $resetUrl
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('KTM eDOIS Password Reset')
            ->view('emails.customer-password-reset');
    }
}
