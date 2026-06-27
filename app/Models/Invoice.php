<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'do_id',
        'cust_id',
        'invoice_number',
        'description',
        'issue_date',
        'subtotal',
        'tax',
        'credit_note',
        'total',
        'status',
        'reason',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'credit_note' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'do_id', 'do_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }

}
