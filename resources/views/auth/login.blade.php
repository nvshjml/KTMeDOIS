<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - KTM eDOIS</title>
    <style>
        :root {
            --ink: #0b1020;
            --muted: #98a2b3;
            --line: #dfe4ec;
            --field: #fbfcfe;
            --focus: #0b4de8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: #111827;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 28px 60px;
            background:
                linear-gradient(90deg, rgba(0, 0, 0, .06), rgba(0, 0, 0, .20)),
                url("{{ asset('images/KTMBg.jpg') }}") center / cover no-repeat;
        }

        .login-card {
            width: min(560px, 100%);
            min-height: 700px;
            padding: 88px 50px 50px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 28px;
            margin-bottom: 74px;
        }

        .brand img {
            width: 132px;
            height: auto;
            flex: 0 0 auto;
        }

        .brand-title {
            font-size: 25px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: 0;
            margin: 0 0 6px;
        }

        .brand-subtitle {
            margin: 0;
            color: #98a2b3;
            font-size: 15px;
            line-height: 1.45;
            font-weight: 600;
        }

        .field-row {
            margin-bottom: 26px;
        }

        .label-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 10px;
        }

        label {
            display: block;
            color: #344054;
            font-size: 16px;
            font-weight: 700;
        }

        .forgot-link {
            display: inline-block;
            margin-top: 9px;
            color: #1f5eff;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .forgot-row {
            min-height: 28px;
            text-align: right;
        }

        .control {
            width: 100%;
            height: 57px;
            border: 1px solid var(--line);
            border-radius: 15px;
            background: var(--field);
            color: #111827;
            font-size: 16px;
            outline: none;
            padding: 0 20px;
            transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .control::placeholder {
            color: #98a2b3;
            font-weight: 500;
        }

        .control:focus {
            border-color: var(--focus);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(11, 77, 232, .18);
        }

        select.control {
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, #667085 50%),
                linear-gradient(135deg, #667085 50%, transparent 50%);
            background-position:
                calc(100% - 27px) 24px,
                calc(100% - 19px) 24px;
            background-size: 8px 8px, 8px 8px;
            background-repeat: no-repeat;
            cursor: pointer;
        }

        select.control:focus {
            border: 3px solid #001c58;
            box-shadow: none;
            padding-left: 18px;
        }

        .login-button {
            width: 100%;
            height: 55px;
            margin-top: 16px;
            border: 0;
            border-radius: 13px;
            background: #080815;
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 7px 14px rgba(8, 8, 21, .18);
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .login-button:hover {
            background: #111323;
            box-shadow: 0 10px 18px rgba(8, 8, 21, .22);
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin: -48px 0 28px;
            font-size: 14px;
            line-height: 1.45;
        }

        .alert-danger {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .alert-success {
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .alert ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .login-hint {
            color: #98a2b3;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.45;
            margin-top: 12px;
        }

        @media (max-width: 980px) {
            .login-page {
                justify-content: center;
                padding: 24px;
            }

            .login-card {
                min-height: auto;
                padding: 54px 34px 38px;
            }

            .brand {
                margin-bottom: 46px;
            }
        }

        @media (max-height: 760px) and (min-width: 981px) {
            .login-page {
                padding-top: 16px;
                padding-bottom: 16px;
            }

            .login-card {
                min-height: auto;
                padding: 56px 50px 36px;
            }

            .brand {
                margin-bottom: 42px;
            }

            .field-row {
                margin-bottom: 20px;
            }

            .control {
                height: 55px;
            }
        }

        @media (max-width: 560px) {
            .login-page {
                align-items: stretch;
                padding: 14px;
            }

            .login-card {
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 34px 22px;
            }

            .brand {
                align-items: flex-start;
                flex-direction: column;
                gap: 16px;
            }

            .brand img {
                width: 118px;
            }
        }
    </style>
</head>
<body>
    <main class="login-page">
        <section class="login-card" aria-label="Customer login">
            <div class="brand">
                <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
                <div>
                    <h1 class="brand-title">KTM eDOIS</h1>
                    <p class="brand-subtitle">Keretapi Tanah Melayu Electronic Delivery<br>Order &amp; Invoice System</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Login failed.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field-row">
                    <label id="login-label" for="login">Username</label>
                    <input
                        id="login"
                        class="control"
                        name="login"
                        type="text"
                        value="{{ old('login') }}"
                        placeholder="Enter your username"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="field-row">
                    <label id="password-label" for="password">Password</label>
                    <input
                        id="password"
                        class="control"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <div class="forgot-row">
                        <a id="forgot-link" class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                </div>

                <div class="field-row">
                    <label for="login_as">Login As</label>
                    <select id="login_as" class="control" name="login_as">
                        <option value="customer" @selected(old('login_as', 'customer') === 'customer')>Customer</option>
                        <option value="supplier" @selected(old('login_as') === 'supplier')>Supplier</option>
                    </select>
                    <div id="login-hint" class="login-hint"></div>
                </div>

                <button class="login-button" type="submit">Login</button>
            </form>
        </section>
    </main>

    <script>
        const loginAs = document.getElementById('login_as');
        const loginLabel = document.getElementById('login-label');
        const loginInput = document.getElementById('login');
        const passwordLabel = document.getElementById('password-label');
        const passwordInput = document.getElementById('password');
        const forgotLink = document.getElementById('forgot-link');
        const hint = document.getElementById('login-hint');

        function applyLoginMode() {
            if (loginAs.value === 'supplier') {
                loginLabel.textContent = 'Vendor Number';
                loginInput.placeholder = 'Enter your vendor number';
                loginInput.autocomplete = 'off';
                passwordLabel.textContent = 'Supplier Email';
                passwordInput.type = 'email';
                passwordInput.placeholder = 'Enter supplier email';
                passwordInput.autocomplete = 'email';
                forgotLink.style.display = 'none';
                hint.textContent = 'Supplier access verifies active vendor master data.';
                return;
            }

            loginLabel.textContent = 'Username';
            loginInput.placeholder = 'Enter your username';
            loginInput.autocomplete = 'username';
            passwordLabel.textContent = 'Password';
            passwordInput.type = 'password';
            passwordInput.placeholder = 'Enter your password';
            passwordInput.autocomplete = 'current-password';
            forgotLink.style.display = 'inline-block';
            hint.textContent = '';
        }

        loginAs.addEventListener('change', applyLoginMode);
        applyLoginMode();
    </script>
</body>
</html>
