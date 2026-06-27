<?php

namespace App\Http\Middleware;

use App\Models\Supplier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupplierSessionMiddleware
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

        return $next($request);
    }
}
