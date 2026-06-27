<?php

namespace App\Models;

use App\Models\Concerns\UsesMainDatabaseConnection;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use UsesMainDatabaseConnection;

    protected $primaryKey = 'do_id';

    protected $fillable = [
        'supplier_id',
        'cust_id',
        'assigned_reviewer_id',
        'assigned_by_id',
        'forwarded_at',
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
        'forwarded_at' => 'datetime',
        'order_date' => 'date',
        'delivery_date' => 'date',
        'items' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'SUPPLIERID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }

    public function assignedReviewer()
    {
        return $this->belongsTo(Customer::class, 'assigned_reviewer_id', 'cust_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Customer::class, 'assigned_by_id', 'cust_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'do_id', 'do_id');
    }
}
