<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - KTM eDOIS</title>
    <style>
        :root {
            --ktm-blue: #003b7a;
            --ktm-blue-deep: #002b5c;
            --ktm-blue-dark: #001a3a;
            --ktm-rail: #ffd200;
            --ktm-rail-dark: #efb900;
            --ink: #0b1020;
            --muted: #98a2b3;
            --line: #dfe4ec;
            --field: #fbfcfe;
            --focus: var(--ktm-blue);
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
                linear-gradient(90deg, rgba(0, 43, 92, .10), rgba(0, 26, 58, .34)),
                url("{{ asset('images/KTMBg.jpg') }}") center / cover no-repeat;
        }

        .login-card {
            width: min(560px, 100%);
            min-height: 700px;
            padding: 88px 50px 50px;
            border-radius: 20px;
            background: #fff;
            border-top: 7px solid var(--ktm-rail);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 20px;
            width: 100%;
            max-width: 100%;
            margin-bottom: 64px;
        }

        .brand img {
            width: clamp(138px, 36%, 168px);
            max-width: 46%;
            height: auto;
            flex: 0 0 auto;
        }

        .brand-copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .brand-title {
            color: var(--ktm-blue-dark);
            font-size: clamp(24px, 2.45vw, 28px);
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: 0;
            margin: 0 0 8px;
            white-space: nowrap;
        }

        .brand-subtitle {
            margin: 0;
            color: #64748b;
            font-size: clamp(14px, 1.45vw, 16px);
            line-height: 1.35;
            font-weight: 700;
            max-width: 100%;
            overflow-wrap: anywhere;
        }

        .brand-subtitle span {
            display: block;
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
            color: var(--ktm-blue);
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
            box-shadow: 0 0 0 3px rgba(0, 59, 122, .18);
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .control {
            padding-right: 58px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #667085;
            cursor: pointer;
            transform: translateY(-50%);
            transition: background .16s ease, color .16s ease;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            background: #eef4ff;
            color: var(--ktm-blue);
            outline: none;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        .password-toggle .eye-off {
            display: none;
        }

        .password-toggle[aria-pressed="true"] .eye {
            display: none;
        }

        .password-toggle[aria-pressed="true"] .eye-off {
            display: block;
        }

        .password-toggle[hidden] {
            display: none;
        }

        .role-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 4px 0 24px;
        }

        .role-button {
            height: 46px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            color: #344054;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: background .16s ease, color .16s ease, border-color .16s ease, box-shadow .16s ease;
        }

        .role-button.is-active {
            border-color: var(--ktm-blue);
            background: var(--ktm-blue);
            color: #fff;
            box-shadow: 0 7px 14px rgba(0, 59, 122, .18);
        }

        .role-button:focus-visible {
            outline: 3px solid rgba(0, 59, 122, .25);
            outline-offset: 2px;
        }

        .login-button {
            width: 100%;
            height: 55px;
            margin-top: 16px;
            border: 0;
            border-radius: 13px;
            background: var(--ktm-blue-deep);
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 7px 14px rgba(0, 43, 92, .2);
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .login-button:hover {
            background: var(--ktm-blue-dark);
            box-shadow: 0 10px 18px rgba(0, 43, 92, .26);
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin: 0 0 24px;
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

        .alert-warning {
            color: #92400e;
            background: #fffbeb;
            border: 1px solid #fde68a;
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
                width: 142px;
                max-width: 72%;
            }
        }
    </style>
</head>
<body>
    <main class="login-page">
        <section class="login-card" aria-label="Admin login">
            <div class="brand">
                <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
                <div class="brand-copy">
                    <h1 class="brand-title">KTM eDOIS</h1>
                    <p class="brand-subtitle">
                        <span>Electronic Delivery Order</span>
                        <span>&amp; Invoice System</span>
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
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
                <input id="login_as" name="login_as" type="hidden" value="{{ old('login_as', request('login_as', 'admin')) }}">

                <div class="field-row">
                    <p>
                        <label id="login-label" for="login">Username</label>
                    </p>
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
                    <p>
                        <label id="password-label" for="password">Password</label>
                    </p>
                    <div class="password-wrap">
                        <input
                            id="password"
                            class="control"
                            name="password"
                            type="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button id="password-toggle" class="password-toggle" type="button" aria-label="Show password" aria-controls="password" aria-pressed="false">
                            <svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="m3 3 18 18"></path>
                                <path d="M10.6 10.6A2 2 0 0 0 13.4 13.4"></path>
                                <path d="M9.9 4.2A10.4 10.4 0 0 1 12 4c6.5 0 10 8 10 8a17.5 17.5 0 0 1-3.1 4.2"></path>
                                <path d="M6.1 6.1C3.4 8 2 12 2 12s3.5 8 10 8a10.7 10.7 0 0 0 4.2-.9"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="forgot-row">
                        <a id="forgot-link" class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                </div>

                <div class="role-buttons" aria-label="Choose login type">
                    <button class="role-button" type="button" data-login-role="admin">Admin</button>
                    <button class="role-button" type="button" data-login-role="supplier">Supplier</button>
                </div>

                <div id="login-hint" class="login-hint"></div>

                <button class="login-button" type="submit">Login</button>
            </form>
        </section>
    </main>

    <script>
        const loginAs = document.getElementById('login_as');
        const roleButtons = document.querySelectorAll('[data-login-role]');
        const loginLabel = document.getElementById('login-label');
        const loginInput = document.getElementById('login');
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const forgotLink = document.getElementById('forgot-link');
        const hint = document.getElementById('login-hint');

        function setPasswordVisible(isVisible) {
            passwordInput.type = isVisible ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
            passwordToggle.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
        }

        function applyLoginMode() {
            roleButtons.forEach((button) => {
                const isActive = button.dataset.loginRole === loginAs.value;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            if (loginAs.value === 'supplier') {
                loginLabel.textContent = 'Username';
                loginInput.placeholder = 'Enter vendor number';
                loginInput.autocomplete = 'off';
                passwordInput.placeholder = 'Enter supplier password';
                passwordInput.autocomplete = 'current-password';
                passwordToggle.hidden = false;
                setPasswordVisible(false);
                forgotLink.style.display = 'inline-block';
                forgotLink.href = "{{ route('password.request') }}?account_type=supplier";
                hint.textContent = 'Supplier username is the vendor number.';
                return;
            }

            loginLabel.textContent = 'Username';
            loginInput.placeholder = 'Enter your username';
            loginInput.autocomplete = 'username';
            passwordInput.placeholder = 'Enter your password';
            passwordInput.autocomplete = 'current-password';
            passwordToggle.hidden = false;
            setPasswordVisible(false);
            forgotLink.style.display = 'inline-block';
            forgotLink.href = "{{ route('password.request') }}?account_type=admin";
            hint.textContent = '';
        }

        passwordToggle.addEventListener('click', () => {
            setPasswordVisible(passwordInput.type === 'password');
        });

        roleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                loginAs.value = button.dataset.loginRole;
                applyLoginMode();
            });
        });

        applyLoginMode();
    </script>
</body>
</html>

