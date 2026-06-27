<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $primaryKey = 'do_id';

    protected $fillable = [
        'supplier_id',
        'do_number',
        'po_number',
        'order_date',
        'invoice_reference',
        'project_reference',
        'shipping_address',
        'invoice_address',
        'items',
        'delivery_date',
        'delivery_time',
        'remarks',
        'do_link',
        'proof_link',
        'status',
        'reason',
        'created_date',
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'order_date' => 'date',
        'delivery_date' => 'date',
        'items' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'do_id', 'do_id');
    }
}
