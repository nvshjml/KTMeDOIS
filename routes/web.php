<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPasswordResetController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SystemHealthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierPortalController;
use App\Http\Controllers\VendorIntegrationController;

Route::get('/health', SystemHealthController::class)->name('system.health');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

// Admin authentication. Admin is the only Laravel-authenticated user.
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store']);
    Route::get('/forgot-password', [CustomerPasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [CustomerPasswordResetController::class, 'sendLink'])->name('password.email');
});

Route::get('/reset-password/{token}', [CustomerPasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [CustomerPasswordResetController::class, 'reset'])->name('password.update');

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('admin.auth')
    ->name('logout');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/delivery-orders', [DeliveryOrderController::class, 'customerIndex'])->name('delivery-orders.index');
    Route::get('/delivery-orders/{id}', [DeliveryOrderController::class, 'customerShow'])->name('delivery-orders.show');
    Route::get('/delivery-orders/{id}/print', [DeliveryOrderController::class, 'customerPrint'])->name('delivery-orders.print');
    Route::post('/delivery-orders/{id}/assign-reviewer', [DeliveryOrderController::class, 'assignReviewer'])->name('delivery-orders.assign-reviewer');
    Route::post('/delivery-orders/{id}/approve', [DeliveryOrderController::class, 'approve'])->name('delivery-orders.approve');
    Route::post('/delivery-orders/{id}/reject', [DeliveryOrderController::class, 'reject'])->name('delivery-orders.reject');
    Route::get('/delivery-orders/{id}/download/{file}', [DeliveryOrderController::class, 'download'])
        ->whereIn('file', ['do', 'proof'])
        ->name('delivery-orders.download');

    Route::get('/invoices', [InvoiceController::class, 'customerIndex'])->name('invoices.index');
    Route::get('/invoices/{id}', [InvoiceController::class, 'customerShow'])->name('invoices.show');
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'customerPrint'])->name('invoices.print');
    Route::post('/invoices/{id}/assign-finance', [InvoiceController::class, 'assignFinance'])->name('invoices.assign-finance');
    Route::post('/invoices/{id}/reject', [InvoiceController::class, 'reject'])->name('invoices.reject');
    Route::post('/invoices/{id}/payment-processing', [InvoiceController::class, 'paymentProcessing'])->name('invoices.payment-processing');
    Route::post('/invoices/{id}/paid', [InvoiceController::class, 'paid'])->name('invoices.paid');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::get('/vendors', [VendorIntegrationController::class, 'index'])->name('vendors');
    Route::post('/vendors/validate', [VendorIntegrationController::class, 'submitValidation'])->name('vendors.validate');
});

// External supplier portal. Suppliers use the shared username/password login.
Route::prefix('supplier')->name('supplier.')->group(function (): void {
    Route::middleware('supplier.session')->group(function (): void {
        Route::get('/profile', [SupplierPortalController::class, 'profile'])->name('profile');
        Route::get('/profile/details', [SupplierPortalController::class, 'details'])->name('details');
        Route::get('/notifications', [SupplierPortalController::class, 'notifications'])->name('notifications');

        Route::get('/delivery-orders/create', [DeliveryOrderController::class, 'supplierCreate'])->middleware('supplier.active')->name('do.create');
        Route::post('/delivery-orders', [DeliveryOrderController::class, 'supplierStore'])->middleware('supplier.active')->name('do.store');
        Route::post('/delivery-orders/{id}/submit-draft', [DeliveryOrderController::class, 'supplierSubmitDraft'])->middleware('supplier.active')->name('do.submit-draft');
        Route::get('/delivery-orders/status', [DeliveryOrderController::class, 'supplierStatus'])->name('do.status');
        Route::get('/delivery-orders/{id}/print', [DeliveryOrderController::class, 'supplierPrint'])->name('do.print');
        Route::get('/delivery-orders/{id}/download/{file}', [DeliveryOrderController::class, 'supplierDownload'])
            ->whereIn('file', ['do', 'proof'])
            ->name('do.download');

        Route::get('/invoices/create/{do_id}', [InvoiceController::class, 'supplierCreate'])->name('invoice.create');
        Route::post('/invoices/preview', [InvoiceController::class, 'supplierPreview'])->name('invoice.preview');
        Route::post('/invoices', [InvoiceController::class, 'supplierStore'])->name('invoice.store');
        Route::get('/invoices/status', [InvoiceController::class, 'supplierStatus'])->name('invoice.status');
        Route::get('/invoices/{id}/edit', [InvoiceController::class, 'supplierEdit'])->name('invoice.edit');
        Route::post('/invoices/{id}', [InvoiceController::class, 'supplierUpdate'])->name('invoice.update');
        Route::get('/invoices/{id}/print', [InvoiceController::class, 'supplierPrint'])->name('invoice.print');

        Route::post('/logout', [SupplierPortalController::class, 'logout'])->name('logout');
    });
});
