<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Supplier extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'supplier_name', 'billing_address', 'vendor_number',
        'contact_person', 'phone', 'email', 'password',
        'status', 'inactive_date',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'inactive_date' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'supplier_id', 'supplier_id');
    }
}