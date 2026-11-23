<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('KUSI - Portal de Transparencia') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --kusi-primary: #6A3FA3;
            --kusi-primary-dark: #4F2C82;
            --kusi-lavender: #B27EEA;
            --kusi-soft: #F4ECFC;
            --kusi-accent: #FBC541;
            --kusi-bg: #FAF7FD;
            --kusi-text: #2D2440;
        }

        body {
            margin: 0;
            font-family: 'Nunito', system-ui, -apple-system, sans-serif;
            background-color: var(--kusi-bg);
            color: var(--kusi-text);
            font-weight: 500;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .font-pacifico {
            font-family: 'Pacifico', cursive;
        }

        .text-purple {
            color: var(--kusi-primary) !important;
        }

        .bg-purple-subtle {
            background-color: var(--kusi-soft) !important;
        }

        .text-amber {
            color: #d97706 !important;
        }

        /* Hero Wrapper */
        .hero-wrap {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #3c1c68 0%, #4F2C82 35%, #6A3FA3 70%, #8B5CC9 100%);
            color: #fff;
            padding-bottom: 95px;
        }

        .hero-shape-1 {
            position: absolute;
            top: -120px;
            right: -100px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(251, 197, 65, 0.22) 0%, rgba(251, 197, 65, 0) 70%);
            pointer-events: none;
        }

        .hero-shape-2 {
            position: absolute;
            bottom: -80px;
            left: -90px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(178, 126, 234, 0.3) 0%, rgba(178, 126, 234, 0) 70%);
            pointer-events: none;
        }

        .hero-logo-box {
            transition: transform 0.3s ease;
        }

        .hero-logo-box:hover {
            transform: scale(1.03);
        }

        /* High-contrast translucent pills on purple background */
        .pill-transparencia {
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #ffffff !important;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.22rem 0.75rem;
            border-radius: 50rem;
            display: inline-block;
            vertical-align: middle;
            letter-spacing: 0.5px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .pill-idioma {
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #ffffff !important;
            padding: 0.35rem 0.95rem;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }

        .pill-idioma a {
            color: #ffffff !important;
            text-decoration: none;
            transition: opacity 0.2s ease, color 0.2s ease;
        }

        .pill-idioma a:hover {
            color: var(--kusi-accent) !important;
        }

        .pill-live {
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #ffffff !important;
            padding: 0.45rem 1.25rem;
            border-radius: 50rem;
            font-size: 0.92rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .pill-live #estado-txt {
            color: #ffffff !important;
            font-weight: 700;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
        }

        /* Radar Live Indicator */
        .punto-radar {
            display: inline-block;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background-color: #adb5bd;
            position: relative;
            transition: background-color 0.3s ease;
            flex-shrink: 0;
        }

        .punto-radar.on {
            background-color: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: radarPulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }

        @keyframes radarPulse {
            to { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 24px;
            background: #ffffff;
            padding: 2.2rem 1.8rem;
            text-align: left;
            box-shadow: 0 12px 32px rgba(106, 63, 163, 0.08);
            border: 1px solid rgba(178, 126, 234, 0.16);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 42px rgba(106, 63, 163, 0.15);
        }

        .stat-card.pulso {
            animation: statBump 0.6s ease;
        }

        @keyframes statBump {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); box-shadow: 0 22px 46px rgba(106, 63, 163, 0.28); border-color: var(--kusi-lavender); }
            100% { transform: scale(1); }
        }

        .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .num {
            font-size: 52px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -1px;
            margin: 0.8rem 0 0.4rem;
        }

        .stat-label {
            font-size: 1.15rem;
            font-weight: 700;
            color: #2D2440;
        }

        .stat-sub {
            font-size: 0.85rem;
            color: #7B6F8F;
        }

        /* Feature Cards */
        .feature-card {
            border-radius: 20px;
            background: #ffffff;
            padding: 2rem 1.6rem;
            box-shadow: 0 8px 24px rgba(106, 63, 163, 0.05);
            border: 1px solid rgba(178, 126, 234, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px rgba(106, 63, 163, 0.1);
        }

        /* Realtime Donation Alert */
        #aviso {
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-bounce {
            animation: bounce 1.5s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-8px); }
            60% { transform: translateY(-4px); }
        }
    </style>
</head>
<body>

    <!-- Hero & Topbar -->
    <header class="hero-wrap">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg py-3">
            <div class="container d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('img/logo-icono.png') }}" alt="KUSI" height="42" class="rounded-3 shadow-sm bg-white p-1">
                    <div>
                        <span class="fs-4 fw-extrabold text-white font-pacifico">Kusi</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    <div class="pill-idioma">
                        @include('partials.idioma')
                    </div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light text-purple fw-bold px-3 py-1_5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-speedometer2"></i>
                            <span>{{ __('Ir al panel') }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-light text-purple fw-bold px-3 py-1_5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>{{ __('Ir al panel') }}</span>
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Content -->
        <div class="container text-center pt-3 pb-4 position-relative" style="z-index: 2;">
            <div class="hero-logo-box mb-3 d-inline-block p-2 rounded-4 bg-white shadow-lg">
            </div>
            <h1 class="display-6 fw-bold text-white mb-2">{{ __('KUSI · Sociedad de Apoyo Social') }}</h1>
            <p class="lead text-white text-opacity-90 mx-auto mb-3" style="max-width: 680px; font-weight: 400; line-height: 1.5;">
                {{ __('Cada donación cuenta. Cada entrega transforma una vida. Consulta en tiempo real el impacto de nuestra labor solidaria.') }}
            </p>
            <div class="pill-live">
                <span class="punto-radar" id="punto"></span>
                <span id="estado-txt">{{ __('Conectando en tiempo real...') }}</span>
            </div>

            <!-- Dynamic Alert for incoming donation -->
            <div id="aviso" class="mt-3 mx-auto alert alert-success border-0 shadow-lg d-none align-items-center justify-content-center gap-2 py-2 px-4 rounded-pill" style="max-width: 540px; background: #e8f8ed; color: #15803d;">
                <i class="bi bi-heart-fill text-danger fs-5 animate-bounce"></i>
                <span id="aviso-texto" class="fw-bold"></span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <!-- Floating Stat Cards -->
        <div class="container position-relative" style="z-index: 3; margin-top: -60px;">
            <div class="row g-4 justify-content-center">
                <!-- Donaciones este mes -->
                <div class="col-md-4">
                    <div class="stat-card" id="c-mes">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-purple-subtle text-purple px-2 py-1 rounded-pill small fw-bold text-uppercase">{{ __('Este mes') }}</span>
                            <div class="stat-icon-wrap bg-purple-subtle text-purple">
                                <i class="bi bi-calendar-heart fs-4"></i>
                            </div>
                        </div>
                        <div class="num text-purple" id="n-mes">{{ $stats['donaciones_mes'] }}</div>
                        <div class="stat-label">{{ __('Donaciones este mes') }}</div>
                        <div class="stat-sub mt-2"><i class="bi bi-clock-history me-1"></i>{{ __('Aportes recibidos durante el mes en curso') }}</div>
                    </div>
                </div>

                <!-- Donaciones totales recibidas -->
                <div class="col-md-4">
                    <div class="stat-card" id="c-total">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill small fw-bold text-uppercase">{{ __('Acumulado') }}</span>
                            <div class="stat-icon-wrap bg-warning-subtle text-warning-emphasis">
                                <i class="bi bi-gift-fill fs-4 text-amber"></i>
                            </div>
                        </div>
                        <div class="num text-amber" id="n-total">{{ $stats['donaciones_total'] }}</div>
                        <div class="stat-label">{{ __('Donaciones totales') }}</div>
                        <div class="stat-sub mt-2"><i class="bi bi-check2-circle me-1"></i>{{ __('Total de donaciones registradas y gestionadas') }}</div>
                    </div>
                </div>

                <!-- Personas apoyadas -->
                <div class="col-md-4">
                    <div class="stat-card" id="c-ben">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success-subtle text-success-emphasis px-2 py-1 rounded-pill small fw-bold text-uppercase">{{ __('Impacto social') }}</span>
                            <div class="stat-icon-wrap bg-success-subtle text-success">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="num text-success" id="n-ben">{{ $stats['beneficiarios'] }}</div>
                        <div class="stat-label">{{ __('Personas apoyadas') }}</div>
                        <div class="stat-sub mt-2"><i class="bi bi-heart me-1"></i>{{ __('Beneficiarios registrados en nuestra comunidad') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transparency Pillars Section -->
        <section class="container py-5 my-2">
            <div class="text-center mb-5">
                <span class="badge bg-purple-subtle text-purple px-3 py-2 rounded-pill fw-bold text-uppercase small mb-2">
                    <i class="bi bi-shield-check me-1"></i>{{ __('Compromiso y Claridad') }}
                </span>
                <h2 class="fw-bold mt-1" style="color: var(--kusi-primary-dark);">{{ __('¿Por qué este portal es público?') }}</h2>
                <p class="text-muted mx-auto" style="max-width: 640px;">
                    {{ __('La confianza de nuestros donantes y de la comunidad es el pilar de KUSI. Por eso compartimos nuestras métricas en tiempo real, sin intermediarios.') }}
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="stat-icon-wrap bg-purple-subtle text-purple mb-3">
                            <i class="bi bi-diagram-3-fill fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ __('Trazabilidad integral') }}</h5>
                        <p class="text-muted small mb-0 lh-base">
                            {{ __('Desde que un producto ingresa como donación hasta que llega a las manos de una familia, todo queda documentado y verificado.') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="stat-icon-wrap bg-warning-subtle text-warning-emphasis mb-3">
                            <i class="bi bi-egg-fried fs-4 text-amber"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ __('Atención a necesidades reales') }}</h5>
                        <p class="text-muted small mb-0 lh-base">
                            {{ __('Apoyamos con raciones de alimentos, productos de primera necesidad y ayudas específicas orientadas a quienes más lo necesitan.') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="stat-icon-wrap bg-success-subtle text-success mb-3">
                            <i class="bi bi-broadcast fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ __('Actualización continua') }}</h5>
                        <p class="text-muted small mb-0 lh-base">
                            {{ __('Esta pantalla se sincroniza automáticamente con cada registro en el sistema, garantizando total transparencia.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Motivational quote card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 text-center mt-5" style="background: linear-gradient(135deg, #F4ECFC 0%, #FFFFFF 100%); border-left: 6px solid var(--kusi-primary) !important;">
                <div class="d-flex flex-column align-items-center">
                    <i class="bi bi-quote fs-1 text-purple opacity-50 mb-0"></i>
                    <p class="fs-5 fst-italic text-dark mb-2 px-md-4" style="max-width: 780px;">
                        {{ __('«Cada registro es una persona. Juntos llevamos esperanza y apoyo solidario a quienes más lo necesitan.»') }}
                    </p>
                    <div class="fw-bold text-purple small">
                        <i class="bi bi-heart-fill text-danger me-1"></i>{{ __('Equipo y Voluntariado KUSI') }}
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-4 border-top mt-auto" style="background: #ffffff; border-color: rgba(178, 126, 234, 0.2) !important;">
        <div class="container text-center">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <img src="{{ asset('img/logo-icono.png') }}" alt="KUSI" height="30" class="rounded-circle">
                <span class="fw-bold font-pacifico text-purple fs-5">Kusi</span>
            </div>
            <p class="text-muted small mb-1">{{ __('KUSI — Sistema de Gestión de Apoyo Social') }}</p>
            <p class="text-muted small mb-2">{{ __('© :year Sociedad de Apoyo Social KUSI. Todos los derechos reservados.', ['year' => date('Y')]) }}</p>
            <div>
                <a href="{{ route('login') }}" class="text-muted text-decoration-none small opacity-75">
                    <i class="bi bi-shield-lock me-1"></i>{{ __('Acceso administrativo') }}
                </a>
            </div>
        </div>
    </footer>

    <!-- Realtime Updates Scripts -->
    <script>
        let anterior = {{ $stats['donaciones_total'] }};
        const txtEnVivo = @json(__('En vivo · Tiempo real'));
        const txtSinConexion = @json(__('Sin conexión'));
        const txtNuevaDonacion = @json(__('¡Nueva donación recibida a las :hora! Gracias.'));

        function pulso(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('pulso');
            setTimeout(() => el.classList.remove('pulso'), 600);
        }

        function marcar(on) {
            const punto = document.getElementById('punto');
            const txt = document.getElementById('estado-txt');
            if (punto) punto.classList.toggle('on', on);
            if (txt) txt.textContent = on ? txtEnVivo : txtSinConexion;
        }

        function mostrarAviso(hora) {
            const aviso = document.getElementById('aviso');
            const texto = document.getElementById('aviso-texto');
            if (!aviso || !texto) return;

            texto.textContent = txtNuevaDonacion.replace(':hora', hora);
            aviso.classList.remove('d-none');
            aviso.classList.add('d-flex');
            setTimeout(() => {
                aviso.classList.remove('d-flex');
                aviso.classList.add('d-none');
            }, 8000);
        }

        async function actualizar() {
            try {
                const r = await fetch("{{ url('publico/datos') }}", { headers: { 'Accept': 'application/json' } });
                if (!r.ok) throw new Error();
                const s = await r.json();

                const campos = { 'n-mes': 'donaciones_mes', 'n-total': 'donaciones_total', 'n-ben': 'beneficiarios' };
                const cards  = { 'n-mes': 'c-mes', 'n-total': 'c-total', 'n-ben': 'c-ben' };

                for (const id in campos) {
                    const el = document.getElementById(id);
                    if (el && String(s[campos[id]]) !== el.textContent) {
                        el.textContent = s[campos[id]];
                        pulso(cards[id]);
                    }
                }

                if (s.donaciones_total > anterior) {
                    const hora = new Date().toLocaleTimeString('{{ app()->getLocale() === "en" ? "en-US" : (app()->getLocale() === "fr" ? "fr-FR" : "es-PE") }}', { hour: '2-digit', minute: '2-digit' });
                    mostrarAviso(hora);
                }
                anterior = s.donaciones_total;

                marcar(true);
            } catch (e) {
                marcar(false);
            }
        }

        actualizar();
        setInterval(actualizar, 5000);
    </script>
</body>
</html>
