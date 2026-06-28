<?php

namespace App\Http\Controllers;

use App\Mail\CustomerPasswordResetMail;
use App\Mail\SupplierPasswordResetMail;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CustomerPasswordResetController extends Controller
{
    public function requestForm(): View
    {
        return view('auth.forgot-password', [
            'accountType' => request('account_type', request('login_as', 'admin')) === 'supplier' ? 'supplier' : 'admin',
        ]);
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_email' => ['required', 'email', 'max:255'],
            'account_type' => ['nullable', 'in:admin,supplier'],
        ], [], [
            'user_email' => 'email address',
        ]);

        $accountType = $validated['account_type'] ?? 'admin';

        if ($accountType === 'supplier') {
            return $this->sendSupplierLink($validated['user_email']);
        }

        $customer = Customer::where('user_email', $validated['user_email'])
            ->where('user_status', 'active')
            ->first();

        if ($customer) {
            $token = Str::random(64);

            $customer->update([
                'reset_token_hash' => Hash::make($token),
                'reset_token_created_at' => now(),
            ]);

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $customer->user_email,
                'account_type' => 'admin',
            ]);

            try {
                Mail::to($customer->user_email)
                    ->send(new CustomerPasswordResetMail($customer, $resetUrl));
            } catch (Throwable) {
                return back()->withErrors([
                    'user_email' => 'Unable to send reset email. Please check the mail settings in .env.',
                ])->withInput();
            }
        }

        return back()->with('success', 'If the email belongs to an active admin account, a reset link has been sent.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
            'accountType' => $request->query('account_type', 'admin') === 'supplier' ? 'supplier' : 'admin',
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'account_type' => ['nullable', 'in:admin,supplier'],
        ]);
        $accountType = $validated['account_type'] ?? 'admin';

        if ($accountType === 'supplier') {
            $supplier = Supplier::where('SUPPLIER_EMAIL_ADD', $validated['email'])->firstOrFail();
            $this->ensureValidResetToken($supplier, $validated['token']);

            $supplier->update([
                'password_hash' => Hash::make($validated['password']),
                'reset_token_hash' => null,
                'reset_token_created_at' => null,
            ]);
        } else {
            $customer = Customer::where('user_email', $validated['email'])
                ->where('user_status', 'active')
                ->firstOrFail();
            $this->ensureValidResetToken($customer, $validated['token']);

            $customer->update([
                'password_hash' => Hash::make($validated['password']),
                'reset_token_hash' => null,
                'reset_token_created_at' => null,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $loginRoute = $accountType === 'supplier'
            ? route('login', ['login_as' => 'supplier'])
            : route('login');

        return redirect($loginRoute)
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }

    private function sendSupplierLink(string $email): RedirectResponse
    {
        $supplier = Supplier::where('SUPPLIER_EMAIL_ADD', $email)->first();

        if ($supplier) {
            $token = Str::random(64);

            $supplier->update([
                'reset_token_hash' => Hash::make($token),
                'reset_token_created_at' => now(),
            ]);

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $supplier->supplier_email,
                'account_type' => 'supplier',
            ]);

            try {
                Mail::to($supplier->supplier_email)
                    ->send(new SupplierPasswordResetMail($supplier, $resetUrl));
            } catch (Throwable) {
                return back()->withErrors([
                    'user_email' => 'Unable to send reset email. Please check the mail settings in .env.',
                ])->withInput();
            }
        }

        return back()->with('success', 'If the email belongs to a supplier account, a reset link has been sent.');
    }

    private function ensureValidResetToken(Customer|Supplier $account, string $token): void
    {
        $expired = ! $account->reset_token_hash
            || ! $account->reset_token_created_at
            || now()->subMinutes(60)->greaterThan($account->reset_token_created_at);

        if ($expired || ! Hash::check($token, $account->reset_token_hash)) {
            throw ValidationException::withMessages([
                'email' => 'This password reset link is invalid or expired.',
            ]);
        }
    }
}
