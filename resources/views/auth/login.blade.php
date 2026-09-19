<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .auth { min-height: 100vh; display: grid; grid-template-columns: 1.1fr 1fr; }
        /* Panel visual */
        .auth-hero {
            position: relative; overflow: hidden; color: #fff; padding: 60px;
            display: flex; flex-direction: column; justify-content: space-between;
            background: linear-gradient(150deg, #0f2f30 0%, #0d9488 60%, #14b8a6 100%);
        }
        .auth-hero::after {
            content: ''; position: absolute; right: -120px; top: -120px; width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(255,255,255,.12), transparent 70%); border-radius: 50%;
        }
        .auth-hero::before {
            content: ''; position: absolute; left: -80px; bottom: -100px; width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(255,255,255,.08), transparent 70%); border-radius: 50%;
        }
        .hero-brand { display: flex; align-items: center; gap: 14px; position: relative; z-index: 2; }
        .hero-brand .logo {
            width: 52px; height: 52px; border-radius: 14px; background: rgba(255,255,255,.15);
            display: grid; place-items: center; font-size: 24px; backdrop-filter: blur(6px);
        }
        .hero-brand b { font-size: 20px; font-weight: 800; letter-spacing: .3px; display: block; }
        .hero-brand span { font-size: 13px; opacity: .8; }
        .hero-main { position: relative; z-index: 2; }
        .hero-main h1 { font-size: 40px; font-weight: 800; line-height: 1.1; margin-bottom: 18px; letter-spacing: -1px; }
        .hero-main p { font-size: 15px; opacity: .9; max-width: 420px; line-height: 1.6; }
        .hero-features { list-style: none; margin-top: 32px; display: flex; flex-direction: column; gap: 14px; }
        .hero-features li { display: flex; align-items: center; gap: 12px; font-size: 14px; opacity: .95; }
        .hero-features i { width: 34px; height: 34px; border-radius: 9px; background: rgba(255,255,255,.15); display: grid; place-items: center; }
        .hero-foot { position: relative; z-index: 2; font-size: 12.5px; opacity: .7; }

        /* Panel formulario */
        .auth-form { display: flex; align-items: center; justify-content: center; padding: 40px; background: #f4f7f8; }
        .form-card { width: 100%; max-width: 400px; }
        .form-card h2 { font-size: 26px; font-weight: 800; margin-bottom: 6px; color: #1f2d33; letter-spacing: -.5px; }
        .form-card .lead { color: #7a8a92; font-size: 14px; margin-bottom: 30px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 7px; color: #38484f; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 15px; top: 15px; color: #9aa8af; font-size: 15px; }
        .input-wrap input {
            width: 100%; height: 50px; border: 1.5px solid #dfe6ea; border-radius: 12px;
            padding: 0 46px; font-size: 14.5px; outline: none; transition: all .15s; background: #fff;
        }
        .input-wrap input:focus { border-color: #0d9488; box-shadow: 0 0 0 4px rgba(13,148,136,.12); }
        .toggle-pass { position: absolute; right: 15px; top: 15px; color: #9aa8af; cursor: pointer; background: none; border: none; font-size: 15px; }
        .row-between { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; font-size: 13px; }
        .remember { display: flex; align-items: center; gap: 8px; color: #38484f; cursor: pointer; }
        .remember input { width: 16px; height: 16px; accent-color: #0d9488; }
        .link { color: #0d9488; font-weight: 600; }
        .btn-submit {
            width: 100%; height: 50px; border: none; border-radius: 12px; cursor: pointer;
            background: linear-gradient(135deg, #0d9488, #14b8a6); color: #fff; font-size: 15px; font-weight: 700;
            box-shadow: 0 8px 20px rgba(13,148,136,.32); transition: all .15s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-submit:hover { filter: brightness(1.05); transform: translateY(-1px); }
        .err-box {
            background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 10px;
            padding: 11px 14px; font-size: 13px; margin-bottom: 18px; display: flex; gap: 9px; align-items: center;
        }
        .demo-box {
            margin-top: 26px; background: #fff; border: 1px dashed #cfd9de; border-radius: 12px; padding: 14px 16px;
            font-size: 12.5px; color: #55666e;
        }
        .demo-box b { color: #0d9488; }
        .demo-box code { background: #eef4f4; padding: 2px 7px; border-radius: 5px; font-size: 12px; color: #0f766e; }
        .demo-box b { color: #0d9488; display: block; margin-bottom: 10px; }
        .quick-list { display: flex; flex-direction: column; gap: 7px; }
        .quick { display: flex; align-items: center; justify-content: space-between; width: 100%; background: #f7fafa; border: 1px solid #e4ecec; border-radius: 9px; padding: 9px 12px; cursor: pointer; transition: all .15s; font-family: inherit; }
        .quick:hover { border-color: #0d9488; background: #fff; }
        .quick span { font-size: 12.5px; font-weight: 600; color: #38484f; }
        .quick em { font-style: normal; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
        .q-admin { background: #ccfbf1; color: #0f766e; }
        .q-mec { background: #e0f2fe; color: #0369a1; }
        .q-super { background: #ede9fe; color: #6d28d9; }
        @media (max-width: 900px) { .auth { grid-template-columns: 1fr; } .auth-hero { display: none; } }
    </style>
</head>
<body>
<div class="auth">
    <div class="auth-hero">
        <div class="hero-brand">
            <div class="logo"><i class="fa-solid fa-car-on"></i></div>
            <div><b>AutoTaller Pro</b><span>Sistema de gestión de taller</span></div>
        </div>
        <div class="hero-main">
            <h1>Gestiona tu taller<br>de forma inteligente</h1>
            <p>Órdenes de servicio, clientes, vehículos, inventario y facturación en una sola plataforma profesional.</p>
            <ul class="hero-features">
                <li><i class="fa-solid fa-clipboard-check"></i> Control total de órdenes de trabajo</li>
                <li><i class="fa-solid fa-warehouse"></i> Inventario y repuestos en tiempo real</li>
                <li><i class="fa-solid fa-chart-line"></i> Reportes e indicadores del negocio</li>
            </ul>
        </div>
        <div class="hero-foot">© {{ date('Y') }} AutoTaller Pro · Todos los derechos reservados</div>
    </div>

    <div class="auth-form">
        <div class="form-card">
            <h2>Bienvenido de nuevo 👋</h2>
            <p class="lead">Ingresa tus credenciales para acceder al panel.</p>

            @if ($errors->any())
                <div class="err-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@taller.com') }}" placeholder="tucorreo@taller.com" required autofocus>
                    </div>
                </div>
                <div class="field">
                    <label for="password">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" value="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-pass" onclick="togglePass()"><i class="fa-solid fa-eye" id="eye"></i></button>
                    </div>
                </div>
                <div class="row-between">
                    <label class="remember"><input type="checkbox" name="remember"> Recordarme</label>
                    <a href="#" class="link">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="btn-submit"><i class="fa-solid fa-right-to-bracket"></i> Ingresar</button>
            </form>

            <div class="demo-box">
                <b><i class="fa-solid fa-bolt"></i> Acceso rápido de demostración</b>
                <div class="quick-list">
                    <button type="button" class="quick" onclick="fill('admin@taller.com')"><span>admin@taller.com</span><em class="q-admin">Admin taller</em></button>
                    <button type="button" class="quick" onclick="fill('carlos@taller.com')"><span>carlos@taller.com</span><em class="q-mec">Mecánico</em></button>
                    <button type="button" class="quick" onclick="fill('superadmin@autotaller.com')"><span>superadmin@autotaller.com</span><em class="q-super">Super Admin</em></button>
                </div>
                <div style="margin-top:8px; font-size:11.5px; color:#8b9aa1;">Contraseña para todas: <code>password</code></div>
            </div>

            <div style="text-align:center; margin-top:18px;">
                <a href="{{ route('landing') }}" class="link" style="font-size:13px;"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
<script>
    function fill(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
        document.getElementById('password').focus();
    }
</script>
<script>
    function togglePass() {
        const inp = document.getElementById('password');
        const eye = document.getElementById('eye');
        if (inp.type === 'password') { inp.type = 'text'; eye.className = 'fa-solid fa-eye-slash'; }
        else { inp.type = 'password'; eye.className = 'fa-solid fa-eye'; }
    }
</script>
</body>
</html>
