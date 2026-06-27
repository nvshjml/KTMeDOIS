<?php

namespace App\Models;

use App\Models\Concerns\UsesMainDatabaseConnection;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use UsesMainDatabaseConnection;

    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'do_id',
        'cust_id',
        'assigned_finance_id',
        'assigned_by_id',
        'forwarded_at',
        'invoice_number',
        'description',
        'issue_date',
        'subtotal',
        'tax',
        'credit_note',
        'penalty',
        'total',
        'status',
        'reason',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'forwarded_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'credit_note' => 'decimal:2',
        'penalty' => 'decimal:2',
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

    public function assignedFinance()
    {
        return $this->belongsTo(Customer::class, 'assigned_finance_id', 'cust_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Customer::class, 'assigned_by_id', 'cust_id');
    }

}
