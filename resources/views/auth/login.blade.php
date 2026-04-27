<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CumiVault</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0f0e17;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(255,107,53,0.07) 0%, transparent 70%);
            top: -100px; left: -100px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(6,214,160,0.05) 0%, transparent 70%);
            bottom: -50px; right: -50px;
            pointer-events: none;
        }

        .login-wrap {
            width: 100%;
            max-width: 420px;
        }

        /* Brand */
        .brand {
            text-align: center;
            margin-bottom: 40px;
        }
        .brand-logo {
            font-family: 'Syne', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #ff6b35;
            letter-spacing: -1px;
        }
        .brand-logo span { color: #fffffe; }
        .brand-sub {
            font-size: 0.8rem;
            color: #a7a5c0;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Card */
        .card {
            background: #1a1927;
            border: 1px solid #2d2b45;
            border-radius: 16px;
            padding: 36px 32px;
        }
        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: #fffffe;
            margin-bottom: 6px;
        }
        .card-sub {
            font-size: 0.85rem;
            color: #a7a5c0;
            margin-bottom: 28px;
        }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #a7a5c0;
            margin-bottom: 7px;
        }
        .form-control {
            width: 100%;
            background: #201e30;
            border: 1px solid #2d2b45;
            border-radius: 10px;
            padding: 12px 14px;
            color: #fffffe;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255,107,53,0.15);
        }
        .form-control.error { border-color: #ef4565; }
        .form-error { font-size: 0.78rem; color: #ef4565; margin-top: 5px; }

        /* Input with icon */
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #a7a5c0;
            font-size: 1rem;
            pointer-events: none;
        }
        .input-wrap .form-control { padding-left: 40px; }
        .toggle-pw {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a7a5c0;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 2px;
        }
        .toggle-pw:hover { color: #fffffe; }

        /* Remember + submit */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #a7a5c0;
            cursor: pointer;
        }
        .remember-label input { accent-color: #ff6b35; width: 15px; height: 15px; }

        .btn-login {
            width: 100%;
            background: #ff6b35;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-login:hover { background: #ff8155; }
        .btn-login:active { transform: scale(0.99); }

        /* Alert */
        .alert {
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-error   { background: rgba(239,69,101,0.1); border: 1px solid rgba(239,69,101,0.3); color: #ef4565; }
        .alert-success { background: rgba(6,214,160,0.1);  border: 1px solid rgba(6,214,160,0.3);  color: #06d6a0; }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.78rem;
            color: #a7a5c0;
        }

        /* Demo accounts hint */
        .demo-hint {
            background: rgba(255,107,53,0.06);
            border: 1px solid rgba(255,107,53,0.2);
            border-radius: 10px;
            padding: 12px 16px;
            margin-top: 20px;
            font-size: 0.78rem;
            color: #a7a5c0;
        }
        .demo-hint strong { color: #ff6b35; font-weight: 600; }
        .demo-row { display: flex; justify-content: space-between; margin-top: 6px; }
        .demo-btn {
            background: rgba(255,107,53,0.12);
            border: 1px solid rgba(255,107,53,0.25);
            border-radius: 6px;
            padding: 4px 10px;
            color: #ff6b35;
            font-size: 0.75rem;
            cursor: pointer;
            font-family: monospace;
        }
        .demo-btn:hover { background: rgba(255,107,53,0.2); }
    </style>
</head>
<body>
    <div class="login-wrap">
        <!-- Brand -->
        <div class="brand">
            <div class="brand-logo">Cumi<span>Vault</span></div>
            <div class="brand-sub">Inventory Mainan</div>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-title">Masuk ke Akun</div>
            <div class="card-sub">Gunakan email & password yang terdaftar</div>

            @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">❌ {{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-error">❌ {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉️</span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-pw" onclick="togglePw()" id="pw-toggle">👁️</button>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-login">Masuk →</button>
            </form>

            <!-- Demo hint -->
            <div class="demo-hint">
                <strong>Akun demo (setelah seeder):</strong>
                <div class="demo-row" style="margin-top:8px">
                    <span>Admin: <code>admin@toyvault.com</code></span>
                    <button class="demo-btn" onclick="fillDemo('admin@toyvault.com','password')">Isi</button>
                </div>
                <div class="demo-row" style="margin-top:4px">
                    <span>Staff: <code>staff@toyvault.com</code></span>
                    <button class="demo-btn" onclick="fillDemo('staff@toyvault.com','password')">Isi</button>
                </div>
            </div>
        </div>

        <div class="login-footer">ToyVault v1.0 &copy; {{ date('Y') }}</div>
    </div>

    <script>
    function togglePw() {
        const pw  = document.getElementById('password');
        const btn = document.getElementById('pw-toggle');
        if (pw.type === 'password') { pw.type = 'text'; btn.textContent = '🙈'; }
        else { pw.type = 'password'; btn.textContent = '👁️'; }
    }
    function fillDemo(email, pw) {
        document.getElementById('email').value    = email;
        document.getElementById('password').value = pw;
    }
    </script>
</body>
</html>
