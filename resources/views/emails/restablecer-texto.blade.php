{{ __('Hola, :nombre', ['nombre' => $nombre]) }}

{{ __('Recibimos una solicitud para restablecer la contraseña de tu usuario ":usuario" en el sistema KUSI (Sociedad de Apoyo Social).', ['usuario' => $usuario]) }}

{{ __('Abre este enlace para elegir una contraseña nueva:') }}
{{ $url }}

{{ __('El enlace vence en :minutos minutos y solo se puede usar una vez.', ['minutos' => $minutos]) }}

{{ __('Si tú no pediste este cambio, ignora este mensaje: tu contraseña seguirá igual.') }}

{{ __('Sociedad de Apoyo Social KUSI') }}
