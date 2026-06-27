<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - KTM eDOIS</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #0b1020;
            background: #111827;
        }
        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 28px 60px;
            background:
                linear-gradient(90deg, rgba(0, 0, 0, .06), rgba(0, 0, 0, .20)),
                url("{{ asset('images/KTMBg.jpg') }}") center / cover no-repeat;
        }
        .card {
            width: min(560px, 100%);
            padding: 58px 50px 50px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 40px;
        }
        .brand img { width: 124px; height: auto; }
        h1 { font-size: 25px; line-height: 1.15; font-weight: 800; margin: 0 0 6px; }
        p { color: #98a2b3; font-size: 15px; line-height: 1.45; font-weight: 600; margin: 0; }
        .field-row { margin-bottom: 20px; }
        label { display: block; color: #344054; font-size: 16px; font-weight: 700; margin-bottom: 10px; }
        .control {
            width: 100%;
            height: 57px;
            border: 1px solid #dfe4ec;
            border-radius: 15px;
            background: #fbfcfe;
            color: #111827;
            font-size: 16px;
            outline: none;
            padding: 0 20px;
        }
        .control:focus {
            border-color: #0b4de8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(11, 77, 232, .18);
        }
        .button {
            width: 100%;
            height: 55px;
            margin-top: 8px;
            border: 0;
            border-radius: 13px;
            background: #080815;
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 7px 14px rgba(8, 8, 21, .18);
        }
        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 22px;
            font-size: 14px;
            line-height: 1.45;
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .alert ul { margin: 6px 0 0; padding-left: 18px; }
        @media (max-width: 980px) {
            .page { justify-content: center; padding: 24px; }
        }
        @media (max-width: 560px) {
            .page { align-items: stretch; padding: 14px; }
            .card { display: flex; flex-direction: column; justify-content: center; padding: 34px 22px; }
            .brand { align-items: flex-start; flex-direction: column; gap: 16px; }
            .brand img { width: 118px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card" aria-label="Reset password">
            <div class="brand">
                <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
                <div>
                    <h1>Reset Password</h1>
                    <p>Create a new customer account password.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert">
                    <strong>Password reset failed.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="field-row">
                    <label for="email">Customer Email</label>
                    <input id="email" class="control" name="email" type="email" value="{{ old('email', $email) }}" required autofocus>
                </div>

                <div class="field-row">
                    <label for="password">New Password</label>
                    <input id="password" class="control" name="password" type="password" placeholder="Enter new password" required>
                </div>

                <div class="field-row">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" class="control" name="password_confirmation" type="password" placeholder="Confirm new password" required>
                </div>

                <button class="button" type="submit">Reset Password</button>
            </form>
        </section>
    </main>
</body>
</html>
