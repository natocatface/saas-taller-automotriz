<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoTaller Pro — Software de gestión para talleres automotrices</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; color: #1f2d33; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .wrap { max-width: 1160px; margin: 0 auto; padding: 0 24px; }
        .btn { display: inline-flex; align-items: center; gap: 9px; padding: 13px 24px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; border: none; transition: all .15s; }
        .btn-primary { background: linear-gradient(135deg, #0d9488, #14b8a6); color: #fff; box-shadow: 0 8px 22px rgba(13,148,136,.35); }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-ghost { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.25); }
        .btn-outline { background: #fff; color: #0f766e; border: 1.5px solid #cfe9e4; }

        /* NAV */
        nav { position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,.9); backdrop-filter: blur(10px); border-bottom: 1px solid #eef2f5; }
        .nav-inner { display: flex; align-items: center; height: 72px; }
        .brand { display: flex; align-items: center; gap: 11px; font-weight: 800; font-size: 19px; }
        .brand .logo { width: 40px; height: 40px; border-radius: 11px; background: linear-gradient(135deg, #0d9488, #22d3ee); display: grid; place-items: center; color: #fff; font-size: 19px; }
        .nav-links { margin-left: auto; display: flex; align-items: center; gap: 28px; }
        .nav-links a.link { font-weight: 600; font-size: 14.5px; color: #55666e; }
        .nav-links a.link:hover { color: #0d9488; }
        @media (max-width: 760px) { .nav-links .link { display: none; } }

        /* HERO */
        .hero { position: relative; overflow: hidden; color: #fff; text-align: center; padding: 90px 0 110px; background: linear-gradient(160deg, #0b2425 0%, #0f2f30 35%, #0d9488 100%); }
        .hero::after { content: ''; position: absolute; right: -140px; top: -140px; width: 460px; height: 460px; background: radial-gradient(circle, rgba(34,211,238,.25), transparent 70%); border-radius: 50%; }
        .hero::before { content: ''; position: absolute; left: -120px; bottom: -160px; width: 420px; height: 420px; background: radial-gradient(circle, rgba(20,184,166,.25), transparent 70%); border-radius: 50%; }
        .hero .inner { position: relative; z-index: 2; }
        .pill { display: inline-block; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); padding: 8px 18px; border-radius: 30px; font-size: 13.5px; font-weight: 600; margin-bottom: 26px; }
        .hero h1 { font-size: 58px; font-weight: 900; line-height: 1.05; letter-spacing: -2px; margin-bottom: 22px; }
        .hero h1 .grad { background: linear-gradient(120deg, #5eead4, #99f6e4); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .hero p.sub { font-size: 18px; opacity: .92; max-width: 620px; margin: 0 auto 36px; }
        .hero-cta { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .hero-stats { display: flex; gap: 50px; justify-content: center; margin-top: 60px; flex-wrap: wrap; }
        .hero-stats .n { font-size: 34px; font-weight: 800; }
        .hero-stats .l { font-size: 13px; opacity: .8; }
        @media (max-width: 640px) { .hero h1 { font-size: 40px; } }

        /* SECTIONS */
        section.pad { padding: 84px 0; }
        .sec-head { text-align: center; max-width: 640px; margin: 0 auto 54px; }
        .sec-head .tag { color: #0d9488; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .sec-head h2 { font-size: 38px; font-weight: 800; letter-spacing: -1px; margin: 10px 0 14px; }
        .sec-head p { color: #6b7c84; font-size: 16.5px; }

        .feat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; }
        .feat { background: #fff; border: 1px solid #eef2f5; border-radius: 16px; padding: 30px 26px; transition: all .18s; }
        .feat:hover { transform: translateY(-4px); box-shadow: 0 16px 34px rgba(16,42,43,.08); }
        .feat .ic { width: 54px; height: 54px; border-radius: 14px; background: #ccfbf1; color: #0d9488; display: grid; place-items: center; font-size: 22px; margin-bottom: 18px; }
        .feat h3 { font-size: 18px; margin-bottom: 8px; }
        .feat p { color: #6b7c84; font-size: 14.5px; }

        .alt { background: #f4f7f8; }

        /* PRICING */
        .price-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; align-items: stretch; }
        .price { background: #fff; border: 1px solid #eef2f5; border-radius: 18px; padding: 32px 28px; display: flex; flex-direction: column; position: relative; }
        .price.pop { border: 2px solid #0d9488; box-shadow: 0 18px 40px rgba(13,148,136,.16); }
        .price .pop-badge { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: #0d9488; color: #fff; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 20px; }
        .price .pname { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #6b7c84; }
        .price .pprice { font-size: 42px; font-weight: 900; letter-spacing: -1.5px; margin: 8px 0 2px; }
        .price .pprice span { font-size: 16px; font-weight: 500; color: #6b7c84; }
        .price .pdesc { color: #6b7c84; font-size: 14px; margin: 12px 0 20px; min-height: 40px; }
        .price ul { list-style: none; flex: 1; margin-bottom: 24px; display: flex; flex-direction: column; gap: 11px; }
        .price li { font-size: 14.5px; display: flex; gap: 10px; align-items: flex-start; }
        .price li i { color: #0d9488; margin-top: 3px; }
        .price .btn { justify-content: center; }

        /* CTA */
        .cta { background: linear-gradient(135deg, #0d9488, #0f766e); color: #fff; text-align: center; border-radius: 24px; padding: 60px 30px; margin: 40px 0; }
        .cta h2 { font-size: 34px; font-weight: 800; margin-bottom: 14px; letter-spacing: -1px; }
        .cta p { opacity: .9; font-size: 17px; margin-bottom: 28px; }

        /* FOOTER */
        footer { background: #0b2425; color: #a7c4c2; padding: 40px 0; text-align: center; font-size: 14px; }
        footer .brand { color: #fff; justify-content: center; margin-bottom: 12px; }
    </style>
</head>
<body>
<nav>
    <div class="wrap nav-inner">
        <div class="brand"><div class="logo"><i class="fa-solid fa-car-on"></i></div> AutoTaller Pro</div>
        <div class="nav-links">
            <a href="#funciones" class="link">Funciones</a>
            <a href="#precios" class="link">Precios</a>
            <a href="{{ route('login') }}" class="link">Iniciar sesión</a>
            <a href="{{ route('login') }}" class="btn btn-primary" style="padding:10px 20px; font-size:14px;"><i class="fa-solid fa-rocket"></i> Prueba gratis</a>
        </div>
    </div>
</nav>

<header class="hero">
    <div class="wrap inner">
        <span class="pill"><i class="fa-solid fa-bolt"></i> Plataforma #1 de gestión para talleres en LATAM</span>
        <h1>Gestiona tu taller<br><span class="grad">de forma inteligente</span></h1>
        <p class="sub">Órdenes de servicio, clientes, vehículos, inventario, facturación y reportes. Todo en una sola plataforma, desde cualquier dispositivo.</p>
        <div class="hero-cta">
            <a href="{{ route('login') }}" class="btn btn-primary"><i class="fa-solid fa-rocket"></i> Comenzar gratis — 30 días</a>
            <a href="#precios" class="btn btn-ghost"><i class="fa-solid fa-tag"></i> Ver precios</a>
        </div>
        <div class="hero-stats">
            <div><div class="n">500+</div><div class="l">Talleres activos</div></div>
            <div><div class="n">120k+</div><div class="l">Órdenes gestionadas</div></div>
            <div><div class="n">99.9%</div><div class="l">Uptime garantizado</div></div>
            <div><div class="n">30 días</div><div class="l">Prueba gratuita</div></div>
        </div>
    </div>
</header>

<section class="pad" id="funciones">
    <div class="wrap">
        <div class="sec-head">
            <div class="tag">Funciones</div>
            <h2>Todo lo que tu taller necesita</h2>
            <p>Una suite completa para digitalizar la operación de tu taller automotriz.</p>
        </div>
        <div class="feat-grid">
            @php
                $features = [
                    ['fa-clipboard-list','Órdenes de servicio','Crea, sigue e imprime órdenes con servicios, repuestos y totales automáticos.'],
                    ['fa-users','Clientes y vehículos','Historial completo por cliente y por vehículo con todos sus servicios.'],
                    ['fa-boxes-stacked','Inventario en tiempo real','Control de stock con alertas de reposición y descuento automático.'],
                    ['fa-file-invoice-dollar','Facturación y caja','Emite boletas y facturas, controla ingresos, egresos y saldos.'],
                    ['fa-calendar-check','Agenda de citas','Calendario visual para organizar las citas y a tus mecánicos.'],
                    ['fa-chart-line','Reportes avanzados','Métricas del negocio, servicios top y productividad en tiempo real.'],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="feat">
                    <div class="ic"><i class="fa-solid {{ $f[0] }}"></i></div>
                    <h3>{{ $f[1] }}</h3>
                    <p>{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="pad alt" id="precios">
    <div class="wrap">
        <div class="sec-head">
            <div class="tag">Precios</div>
            <h2>Planes para cada tamaño de taller</h2>
            <p>Empieza gratis por 30 días. Sin tarjeta de crédito, cancela cuando quieras.</p>
        </div>
        <div class="price-grid">
            @forelse ($planes as $plan)
                <div class="price {{ $plan->destacado ? 'pop' : '' }}">
                    @if ($plan->destacado)<div class="pop-badge">Más popular</div>@endif
                    <div class="pname">{{ $plan->nombre }}</div>
                    <div class="pprice">S/ {{ number_format($plan->precio_mensual, 0) }}<span>/mes</span></div>
                    <div class="pdesc">{{ $plan->descripcion }}</div>
                    <ul>
                        @foreach ($plan->lista_caracteristicas as $c)
                            <li><i class="fa-solid fa-circle-check"></i> {{ $c }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('login') }}" class="btn {{ $plan->destacado ? 'btn-primary' : 'btn-outline' }}">Empezar ahora</a>
                </div>
            @empty
                <div class="price"><div class="pname">Planes</div><div class="pdesc">Ejecuta el seeder para ver los planes disponibles.</div><a href="{{ route('login') }}" class="btn btn-primary">Iniciar sesión</a></div>
            @endforelse
        </div>
    </div>
</section>

<section>
    <div class="wrap">
        <div class="cta">
            <h2>¿Listo para modernizar tu taller?</h2>
            <p>Únete a cientos de talleres que ya gestionan su negocio con AutoTaller Pro.</p>
            <a href="{{ route('login') }}" class="btn btn-outline" style="font-size:16px;"><i class="fa-solid fa-rocket"></i> Comenzar mi prueba gratuita</a>
        </div>
    </div>
</section>

<footer>
    <div class="wrap">
        <div class="brand"><div class="logo"><i class="fa-solid fa-car-on"></i></div> AutoTaller Pro</div>
        <div>© {{ date('Y') }} AutoTaller Pro · Software de gestión para talleres automotrices · Hecho en LATAM</div>
    </div>
</footer>
</body>
</html>
