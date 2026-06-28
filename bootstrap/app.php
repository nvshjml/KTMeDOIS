<?php

use App\Http\Middleware\ActiveVendor;
use App\Http\Middleware\CustomerAuthMiddleware;
use App\Http\Middleware\SupplierSessionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'customer.auth' => CustomerAuthMiddleware::class,
            'supplier.active' => ActiveVendor::class,
            'supplier.session' => SupplierSessionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
