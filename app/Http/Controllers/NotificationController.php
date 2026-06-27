<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::where('cust_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('customer.notifications', compact('notifications'));
    }

    public function markRead(int $id): RedirectResponse
    {
        $notification = Notification::where('cust_id', auth()->id())->findOrFail($id);

        $notification->update(['status' => 'read']);

        return back()->with('success', 'Marked as read.');
    }
}
