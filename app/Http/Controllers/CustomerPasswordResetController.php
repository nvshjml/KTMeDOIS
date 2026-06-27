<?php

namespace App\Http\Controllers;

use App\Mail\CustomerPasswordResetMail;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CustomerPasswordResetController extends Controller
{
    public function requestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_email' => ['required', 'email', 'max:255'],
        ], [], [
            'user_email' => 'email address',
        ]);

        $customer = Customer::where('user_email', $validated['user_email'])
            ->where('user_status', 'active')
            ->first();

        if ($customer) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $customer->user_email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $customer->user_email,
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

        return back()->with('success', 'If the email belongs to an active customer, a reset link has been sent.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        $expired = ! $tokenRecord || now()->subMinutes(60)->greaterThan($tokenRecord->created_at);

        if ($expired || ! Hash::check($validated['token'], $tokenRecord->token)) {
            throw ValidationException::withMessages([
                'email' => 'This password reset link is invalid or expired.',
            ]);
        }

        $customer = Customer::where('user_email', $validated['email'])
            ->where('user_status', 'active')
            ->firstOrFail();

        $customer->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password reset successfully. Please login with your new password.');
    }
}
