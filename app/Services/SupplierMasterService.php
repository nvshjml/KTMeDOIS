<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierMasterService
{
    public function findByVendorAndEmail(string $vendorNumber, string $email): ?Supplier
    {
        return Supplier::where('vendor_number', $vendorNumber)
            ->where('supplier_email', $email)
            ->first();
    }

    public function activeSupplier(string $vendorNumber, string $email): ?Supplier
    {
        $supplier = $this->findByVendorAndEmail($vendorNumber, $email);

        if (! $supplier || ! $supplier->isActive()) {
            return null;
        }

        return $supplier;
    }
}
