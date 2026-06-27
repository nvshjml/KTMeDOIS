<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPasswordResetController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupplierPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('customer.dashboard')
        : redirect()->route('login');
});

// Customer authentication. Customer is the only Laravel-authenticated user.
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store']);
    Route::get('/forgot-password', [CustomerPasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [CustomerPasswordResetController::class, 'sendLink'])->name('password.email');
});

Route::get('/reset-password/{token}', [CustomerPasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [CustomerPasswordResetController::class, 'reset'])->name('password.update');

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('customer.auth')
    ->name('logout');

Route::middleware('customer.auth')->prefix('customer')->name('customer.')->group(function (): void {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/delivery-orders', [DeliveryOrderController::class, 'customerIndex'])->name('delivery-orders.index');
    Route::get('/delivery-orders/{id}', [DeliveryOrderController::class, 'customerShow'])->name('delivery-orders.show');
    Route::post('/delivery-orders/{id}/approve', [DeliveryOrderController::class, 'approve'])->name('delivery-orders.approve');
    Route::post('/delivery-orders/{id}/reject', [DeliveryOrderController::class, 'reject'])->name('delivery-orders.reject');
    Route::get('/delivery-orders/{id}/download/{file}', [DeliveryOrderController::class, 'download'])
        ->whereIn('file', ['do', 'proof'])
        ->name('delivery-orders.download');

    Route::get('/invoices', [InvoiceController::class, 'customerIndex'])->name('invoices.index');
    Route::get('/invoices/{id}', [InvoiceController::class, 'customerShow'])->name('invoices.show');
    Route::post('/invoices/{id}/reject', [InvoiceController::class, 'reject'])->name('invoices.reject');
    Route::post('/invoices/{id}/payment-processing', [InvoiceController::class, 'paymentProcessing'])->name('invoices.payment-processing');
    Route::post('/invoices/{id}/paid', [InvoiceController::class, 'paid'])->name('invoices.paid');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

// External supplier portal. Supplier is verified from master data, not logged in as a Laravel user.
Route::prefix('supplier')->name('supplier.')->group(function (): void {
    Route::get('/verify', [SupplierPortalController::class, 'verifyForm'])->name('verify');
    Route::post('/verify', [SupplierPortalController::class, 'verify'])->name('verify.store');

    Route::middleware('supplier.active')->group(function (): void {
        Route::get('/profile', [SupplierPortalController::class, 'profile'])->name('profile');

        Route::get('/delivery-orders/create', [DeliveryOrderController::class, 'supplierCreate'])->name('do.create');
        Route::post('/delivery-orders', [DeliveryOrderController::class, 'supplierStore'])->name('do.store');
        Route::get('/delivery-orders/status', [DeliveryOrderController::class, 'supplierStatus'])->name('do.status');

        Route::get('/invoices/create/{do_id}', [InvoiceController::class, 'supplierCreate'])->name('invoice.create');
        Route::post('/invoices', [InvoiceController::class, 'supplierStore'])->name('invoice.store');
        Route::get('/invoices/status', [InvoiceController::class, 'supplierStatus'])->name('invoice.status');

        Route::post('/logout', [SupplierPortalController::class, 'logout'])->name('logout');
    });
});
