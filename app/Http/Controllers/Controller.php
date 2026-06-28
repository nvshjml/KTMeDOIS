<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Support\Collection;

abstract class Controller
{
    protected function matchingSupplierIds(string $search): Collection
    {
        $search = trim($search);

        if ($search === '') {
            return collect();
        }

        return Supplier::query()
            ->where('SUPPLIER_COMP_NAME', 'like', "%{$search}%")
            ->orWhere('SUPPLIERID', 'like', "%{$search}%")
            ->limit(100)
            ->pluck('SUPPLIERID');
    }
}
