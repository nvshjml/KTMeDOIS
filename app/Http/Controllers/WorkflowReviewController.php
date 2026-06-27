<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;

class WorkflowReviewController extends Controller
{
    public function index()
    {
        $pendingDOs = DeliveryOrder::with('supplier')
            ->where('status', 'Pending Approval')
            ->orderBy('created_at', 'asc')
            ->get();

        $pendingInvoices = Invoice::with('supplier')
            ->where('status', 'Submitted')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('finance.do.index', compact('pendingDOs', 'pendingInvoices'));
    }
}
