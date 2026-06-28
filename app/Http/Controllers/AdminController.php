<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /** User Management */
    public function users()
    {
        $users = Customer::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:customers,username',
            'email'    => 'required|email|unique:customers,user_email',
            'role'     => 'required|in:admin,reviewer,finance',
            'password' => 'required|string|min:4|confirmed',
        ]);

        $customer = Customer::create([
            'display_name' => $request->name,
            'username' => $request->username,
            'user_email' => $request->email,
            'user_role' => $request->role,
            'password_hash' => Hash::make($request->password),
            'user_status' => 'active',
        ]);

        AuditLog::create([
            'cust_id' => auth()->id(),
            'action' => 'Created new user: '.$request->username,
            'affected_record' => 'customers:'.$customer->cust_id,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function destroyUser(Customer $user)
    {
        $user->delete();
        AuditLog::create([
            'cust_id' => auth()->id(),
            'action' => 'Deleted user: '.$user->username,
            'affected_record' => 'customers:'.$user->cust_id,
        ]);
        return back()->with('success', 'User deleted.');
    }

    /** Vendor Registry */
    public function vendors()
    {
        $vendors = Supplier::orderBy('SUPPLIER_COMP_NAME')->get();
        return view('admin.vendors', compact('vendors'));
    }

    /** Audit Logs */
    public function audit()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->paginate(30);
        return view('admin.audit', compact('logs'));
    }

    /** System Config */
    public function config()
    {
        return view('admin.config');
    }

    /** Stats for dashboard */
    public static function dashboardStats(): array
    {
        return [
            'total_users'    => Customer::count(),
            'active_users'   => Customer::where('user_status', 'active')->count(),
            'active_vendors' => Supplier::where('SUPPLIER_CTC_STATUS', 'active')->count(),
            'total_vendors'  => Supplier::count(),
            'pending_dos'    => DeliveryOrder::where('status', 'Pending Approval')->count(),
            'total_invoices' => Invoice::count(),
            'total_value'    => Invoice::sum('total'),
        ];
    }
}
