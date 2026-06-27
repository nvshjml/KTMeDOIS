<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ReportGeneratorController extends Controller
{
    public function index()
    {
        $stats = [
            'total_dos'          => DeliveryOrder::count(),
            'approved_dos'       => DeliveryOrder::where('status', 'Approved')->count(),
            'pending_dos'        => DeliveryOrder::where('status', 'Pending Approval')->count(),
            'rejected_dos'       => DeliveryOrder::where('status', 'Rejected')->count(),
            'total_invoices'     => Invoice::count(),
            'paid_invoices'      => Invoice::where('status', 'Paid')->count(),
            'pending_invoices'   => Invoice::where('status', 'Submitted')->count(),
            'total_invoice_value'=> Invoice::sum('total'),
            'paid_invoice_value' => Invoice::where('status', 'Paid')->sum('total'),
        ];

        return view('finance.reports', compact('stats'));
    }
}
