<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierMasterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class VendorIntegrationController extends Controller
{
    public function index(): View
    {
        $vendors = Supplier::orderBy('SUPPLIER_COMP_NAME')->get();

        return view('admin.vendors', compact('vendors'));
    }

    public function submitValidation(Request $request, SupplierMasterService $supplierMasterService): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'ssm_number' => ['required', 'string', 'max:200'],
        ]);

        try {
            $supplier = $supplierMasterService->findByCompanyAndSsm(
                $validated['company_name'],
                $validated['ssm_number']
            );
        } catch (Throwable) {
            return back()
                ->with('error', 'API connection failed or supplier not found.')
                ->withInput();
        }

        if (! $supplier) {
            return back()
                ->with('error', 'API connection failed or supplier not found.')
                ->withInput();
        }

        $request->session()->put('validation_success', true);
        $request->session()->put('validated_supplier_id', $supplier->supplier_id);
        $request->session()->put('supplier_id', $supplier->supplier_id);

        return redirect()->route('supplier.do.status');
    }
}
