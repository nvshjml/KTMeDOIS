<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierMasterService
{
    public function findByVendorNumber(string $vendorNumber): ?Supplier
    {
        return Supplier::where('SUPPLIERID', $vendorNumber)->first();
    }

    public function findByVendorAndEmail(string $vendorNumber, string $email): ?Supplier
    {
        return Supplier::where('SUPPLIERID', $vendorNumber)
            ->where('SUPPLIER_EMAIL_ADD', $email)
            ->first();
    }

    public function findByCompanyAndSsm(string $companyName, string $ssmNumber): ?Supplier
    {
        return Supplier::where('SUPPLIER_COMP_NAME', $companyName)
            ->where('SUPPLIER_COMP_REG_NO', $ssmNumber)
            ->first();
    }

    public function activeSupplier(string $vendorNumber, ?string $email = null): ?Supplier
    {
        $supplier = $email
            ? $this->findByVendorAndEmail($vendorNumber, $email)
            : $this->findByVendorNumber($vendorNumber);

        if (! $supplier || ! $supplier->isActive()) {
            return null;
        }

        return $supplier;
    }
}
