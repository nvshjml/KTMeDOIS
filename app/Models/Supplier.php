<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'supplier_name',
        'billing_address',
        'vendor_number',
        'contact_person',
        'supplier_phone',
        'supplier_email',
        'supplier_status',
        'inactive_date',
    ];

    protected $casts = [
        'inactive_date' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->supplier_status === 'active';
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'supplier_id', 'supplier_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'supplier_id', 'supplier_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'supplier_id', 'supplier_id');
    }
}
