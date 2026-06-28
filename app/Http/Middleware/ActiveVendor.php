<?php

namespace App\Http\Middleware;

use App\Models\Supplier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $supplierId = $request->session()->get('supplier_id');
        $supplier = $supplierId ? Supplier::find($supplierId) : null;

        if (! $supplier) {
            $request->session()->forget('supplier_id');

            return redirect()
                ->route('login', ['login_as' => 'supplier'])
                ->with('error', 'Please login as a supplier first.');
        }

        if (! $supplier->isActive()) {
            return redirect()
                ->route('supplier.do.status')
                ->with('error', 'This supplier is inactive and cannot upload Delivery Orders.');
        }

        return $next($request);
    }
}
