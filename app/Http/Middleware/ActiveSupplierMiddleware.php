<?php

namespace App\Http\Middleware;

use App\Models\Supplier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveSupplierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $supplierId = $request->session()->get('supplier_id');
        $supplier = $supplierId ? Supplier::find($supplierId) : null;

        if (! $supplier) {
            return redirect()->route('supplier.verify')->with('error', 'Please verify your supplier details first.');
        }

        if (! $supplier->isActive()) {
            $request->session()->forget('supplier_id');

            return redirect()->route('supplier.verify')->with('error', 'This supplier is inactive and cannot submit documents.');
        }

        return $next($request);
    }
}
