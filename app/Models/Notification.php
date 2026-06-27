<?php

namespace App\Models;

use App\Models\Concerns\UsesMainDatabaseConnection;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use UsesMainDatabaseConnection;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'supplier_id',
        'cust_id',
        'type',
        'content',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'SUPPLIERID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }
}
