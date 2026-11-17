<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('KUSI - Transparencia') }}</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f5f0fb; color: #3b2a63; }
        header { background: #5b2d9e; color: #fff; padding: 32px 20px; text-align: center; position: relative; }
        header h1 { margin: 0 0 6px; }
        .lang-switch { position: absolute; top: 16px; right: 20px; font-size: 14px; font-weight: 600; }
        main { max-width: 900px; margin: 30px auto; padding: 0 16px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
        .card { background: #fff; border-radius: 16px; padding: 24px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.06); transition: transform .3s; }
        .card.pulso { transform: scale(1.06); }
        .num { font-size: 48px; font-weight: 700; color: #5b2d9e; }
        .estado { text-align: center; margin-top: 20px; font-size: 14px; }
        .punto { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #aaa; margin-right: 6px; }
        .punto.on { background: #22c55e; }
        #aviso { display: none; margin: 20px auto 0; max-width: 520px; background: #e9f9ee; border: 1px solid #22c55e; color: #166534; padding: 12px 16px; border-radius: 12px; text-align: center; }
    </style>
</head>
<body>
    <header>
        <div class="lang-switch">
            @include('partials.idioma')
        </div>
        <h1>{{ __('KUSI · Sociedad de Apoyo Social') }}</h1>
        <p>{{ __('Cada donación cuenta. Así avanzamos, en tiempo real.') }}</p>
    </header>

    <main>
        <div class="grid">
            <div class="card" id="c-mes">
                <div class="num" id="n-mes">{{ $stats['donaciones_mes'] }}</div>
                <div>{{ __('Donaciones este mes') }}</div>
            </div>
            <div class="card" id="c-total">
                <div class="num" id="n-total">{{ $stats['donaciones_total'] }}</div>
                <div>{{ __('Donaciones recibidas') }}</div>
            </div>
            <div class="card" id="c-ben">
                <div class="num" id="n-ben">{{ $stats['beneficiarios'] }}</div>
                <div>{{ __('Personas apoyadas') }}</div>
            </div>
        </div>

        <div id="aviso"></div>

        <p class="estado"><span class="punto" id="punto"></span><span id="estado-txt">{{ __('Conectando...') }}</span></p>
    </main>

    <script>
        let anterior = {{ $stats['donaciones_total'] }};
        const txtEnVivo = @json(__('En vivo'));
        const txtSinConexion = @json(__('Sin conexión'));
        const txtNuevaDonacion = @json(__('¡Nueva donación recibida a las :hora! Gracias.'));

        function pulso(id) {
            const el = document.getElementById(id);
            el.classList.add('pulso');
            setTimeout(() => el.classList.remove('pulso'), 600);
        }

        function marcar(on) {
            document.getElementById('punto').classList.toggle('on', on);
            document.getElementById('estado-txt').textContent = on ? txtEnVivo : txtSinConexion;
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
                    if (String(s[campos[id]]) !== el.textContent) {
                        el.textContent = s[campos[id]];
                        pulso(cards[id]);
                    }
                }

                if (s.donaciones_total > anterior) {
                    const aviso = document.getElementById('aviso');
                    const hora = new Date().toLocaleTimeString('{{ app()->getLocale() === "en" ? "en-US" : (app()->getLocale() === "fr" ? "fr-FR" : "es-PE") }}', { hour: '2-digit', minute: '2-digit' });
                    aviso.textContent = txtNuevaDonacion.replace(':hora', hora);
                    aviso.style.display = 'block';
                    setTimeout(() => aviso.style.display = 'none', 8000);
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
