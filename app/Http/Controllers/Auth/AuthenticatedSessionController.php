<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        return app(AuthController::class)->store(
            $request,
            app(\App\Services\AuditService::class),
            app(\App\Services\SupplierMasterService::class)
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        return app(AuthController::class)->destroy($request);
    }
}
