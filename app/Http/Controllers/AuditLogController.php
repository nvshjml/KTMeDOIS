<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $auditLogs = AuditLog::with('customer', 'supplier')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');
                $query->where(function ($inner) use ($search): void {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('affected_record', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                            $customerQuery->where('username', 'like', "%{$search}%")
                                ->orWhere('user_email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('SUPPLIER_COMP_NAME', 'like', "%{$search}%")
                                ->orWhere('SUPPLIERID', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request): void {
                $query->whereBetween('created_at', [
                    $request->date('start_date')->startOfDay(),
                    $request->date('end_date')->endOfDay(),
                ]);
            })
            ->latest('timestamp')
            ->paginate(20)
            ->withQueryString();

        return view('customer.audit-logs', compact('auditLogs'));
    }
}
