<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifier' => 'required|string', // username OR email
            'password'   => 'required|string',
        ]);

        // 1. Try staff/KTM users first (they log in by username)
        if (Auth::guard('web')->attempt([
            'username' => $credentials['identifier'],
            'password' => $credentials['password'],
            'status'   => 'active',
        ])) {
            $request->session()->regenerate();
            Auth::guard('web')->user()->update(['last_login' => now()]);
            return redirect()->intended(route('admin.dashboard'));
        }

        // 2. Try suppliers/vendors (they log in by registered email)
        if (Auth::guard('supplier')->attempt([
            'email'    => $credentials['identifier'],
            'password' => $credentials['password'],
            'status'   => 'active',
        ])) {
            $request->session()->regenerate();
            return redirect()->intended(route('supplier.dashboard'));
        }

        return back()
            ->withErrors(['identifier' => 'Invalid credentials, or your account is inactive.'])
            ->onlyInput('identifier');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        if (Auth::guard('supplier')->check()) {
            Auth::guard('supplier')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
