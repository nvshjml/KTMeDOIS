<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Supplier;

class AuditService
{
    public function record(
        string $action,
        string $affectedRecord,
        ?Customer $customer = null,
        ?Supplier $supplier = null
    ): AuditLog {
        return AuditLog::create([
            'cust_id' => $customer?->cust_id,
            'supplier_id' => $supplier?->supplier_id,
            'action' => $action,
            'affected_record' => $affectedRecord,
            'timestamp' => now(),
        ]);
    }
}
