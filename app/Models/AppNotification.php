<?php

namespace App\Models;

use App\Models\Concerns\UsesMainDatabaseConnection;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use UsesMainDatabaseConnection;

    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'supplier_id',
        'user_id',
        'type',
        'content',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'SUPPLIERID');
    }

    public function user()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }
}
