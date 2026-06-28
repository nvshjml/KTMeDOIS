<p>Hello {{ $customer->username }},</p>

<p>You requested a password reset for your KTM eDOIS admin account.</p>

<p>
    <a href="{{ $resetUrl }}">Reset your password</a>
</p>

<p>This reset link will expire in 60 minutes.</p>

<p>If you did not request this, you can ignore this email.</p>
