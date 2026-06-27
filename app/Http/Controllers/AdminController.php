<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** User Management */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            'role'     => 'required|in:admin,finance,customer,supplier',
            'password' => 'required|string|min:4|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => bcrypt($request->password),
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'user_name'       => auth()->user()->name,
            'action'          => 'Created new user: ' . $request->username,
            'affected_record' => 'users',
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        AuditLog::create([
            'user_id'         => auth()->id(),
            'user_name'       => auth()->user()->name,
            'action'          => 'Deleted user: ' . $user->username,
            'affected_record' => 'users',
        ]);
        return back()->with('success', 'User deleted.');
    }

    /** Vendor Registry */
    public function vendors()
    {
        $vendors = User::where('role', 'supplier')->orderBy('name')->get();
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
            'total_users'    => User::count(),
            'active_users'   => User::where('role', '!=', 'supplier')->count(),
            'active_vendors' => User::where('role', 'supplier')->count(),
            'total_vendors'  => User::where('role', 'supplier')->count(),
            'pending_dos'    => DeliveryOrder::where('status', 'Pending Approval')->count(),
            'total_invoices' => Invoice::count(),
            'total_value'    => Invoice::sum('total'),
        ];
    }
}
