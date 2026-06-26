<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/users',   fn() => view('admin.users'))->name('users');
        Route::get('/vendors', fn() => view('admin.vendors'))->name('vendors');
        Route::get('/audit',   fn() => view('admin.audit'))->name('audit');
        Route::get('/config',  fn() => view('admin.config'))->name('config');
    });

    // Finance routes
    Route::prefix('finance')->name('finance.')->middleware('role:finance')->group(function () {
        Route::get('/do',       fn() => view('finance.do'))->name('do');
        Route::get('/invoices', fn() => view('finance.invoices'))->name('invoices');
        Route::get('/reports',  fn() => view('finance.reports'))->name('reports');
        Route::get('/notif',    fn() => view('finance.notifications'))->name('notif');
    });

    // Customer/Vendor routes
    Route::prefix('vendor')->name('customer.')->middleware('role:customer')->group(function () {
        Route::get('/profile',         fn() => view('customer.profile'))->name('profile');
        Route::get('/do',              fn() => view('customer.do.index'))->name('do.index');
        Route::get('/do/create',       fn() => view('customer.do.create'))->name('do.create');
        Route::get('/invoices',        fn() => view('customer.invoices.index'))->name('inv.index');
        Route::get('/invoices/create', fn() => view('customer.invoices.create'))->name('inv.create');
        Route::get('/notifications',   fn() => view('customer.notifications'))->name('notif');
        Route::get('/help',            fn() => view('customer.help'))->name('help');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';