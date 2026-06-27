<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'cust_id';

    protected $fillable = [
        'username',
        'display_name',
        'password_hash',
        'user_role',
        'user_email',
        'user_status',
        'last_login',
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function isActive(): bool
    {
        return $this->user_status === 'active';
    }

    public function getNameAttribute(): ?string
    {
        return $this->display_name ?? $this->username;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user_email;
    }

    public function getRoleAttribute(): ?string
    {
        return $this->user_role;
    }

    public function getIdAttribute(): int|string|null
    {
        return $this->cust_id;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'cust_id', 'cust_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'cust_id', 'cust_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'cust_id', 'cust_id');
    }
}
