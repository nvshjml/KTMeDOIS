<?php

namespace App\Mail;

use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Supplier $supplier,
        public string $resetUrl
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('KTM eDOIS Supplier Password Reset')
            ->view('emails.supplier-password-reset');
    }
}
