<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - KTM eDOIS</title>
    <style>
        * { box-sizing: border-box; }
        :root {
            --ktm-blue: #003b7a;
            --ktm-blue-deep: #002b5c;
            --ktm-blue-dark: #001a3a;
            --ktm-rail: #ffd200;
        }
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
            justify-content: center;
            padding: 28px;
            background:
                linear-gradient(90deg, rgba(0, 43, 92, .10), rgba(0, 26, 58, .34)),
                url("{{ asset('images/KTMBg.jpg') }}") center / cover no-repeat;
        }
        .card {
            width: min(560px, 100%);
            position: relative;
            padding: 58px 50px 50px;
            border-radius: 20px;
            background: #fff;
            border-top: 7px solid var(--ktm-rail);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 40px;
        }
        .brand img { width: 124px; height: auto; }
        h1 { color: var(--ktm-blue-dark); font-size: 25px; line-height: 1.15; font-weight: 800; margin: 0 0 6px; }
        p { color: #98a2b3; font-size: 15px; line-height: 1.45; font-weight: 600; margin: 0; }
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
            border-color: var(--ktm-blue);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 59, 122, .18);
        }
        .button {
            width: 100%;
            height: 55px;
            margin-top: 24px;
            border: 0;
            border-radius: 13px;
            background: var(--ktm-blue-deep);
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 7px 14px rgba(0, 43, 92, .2);
        }
        .back-link {
            display: inline-block;
            margin-top: 18px;
            color: var(--ktm-blue);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }
        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 22px;
            font-size: 14px;
            line-height: 1.45;
        }
        .alert-danger { color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; }
        .alert-success { color: #166534; background: #f0fdf4; border: 1px solid #bbf7d0; }
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
        <section class="card" aria-label="Forgot password">
            <div class="brand">
                <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
                <div>
                    <h1>Forgot Password</h1>
                    <p>Enter your {{ $accountType === 'supplier' ? 'supplier' : 'admin' }} email to receive a reset link.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Unable to send reset link.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="hidden" name="account_type" value="{{ $accountType }}">
                <label for="user_email">{{ $accountType === 'supplier' ? 'Supplier Email' : 'Admin Email' }}</label>
                <input id="user_email" class="control" name="user_email" type="email" value="{{ old('user_email') }}" placeholder="Enter your {{ $accountType === 'supplier' ? 'supplier' : 'admin' }} email" required autofocus>
                <button class="button" type="submit">Send Reset Link</button>
            </form>

            <a class="back-link" href="{{ route('login', ['login_as' => $accountType === 'supplier' ? 'supplier' : 'admin']) }}">Back to login</a>
        </section>
    </main>
</body>
</html>
