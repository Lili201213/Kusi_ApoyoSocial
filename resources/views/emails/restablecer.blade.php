<!DOCTYPE html>
<html lang="es">
<body style="margin:0;padding:0;background:#FAF7FD;font-family:Arial,Helvetica,sans-serif;color:#2D2440;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF7FD;padding:24px 0;">
    <tr><td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #EADFF7;">
            <tr>
                <td style="background:#6A3FA3;padding:24px 28px;color:#ffffff;">
                    <div style="font-size:30px;font-weight:bold;font-style:italic;">Kusi</div>
                    <div style="font-size:13px;color:#E6D8FA;">Sociedad de Apoyo Social</div>
                </td>
            </tr>
            <tr>
                <td style="padding:30px 28px;">
                    <h2 style="margin:0 0 14px;color:#4F2C82;font-size:22px;">Hola, {{ $nombre }}</h2>
                    <p style="margin:0 0 12px;line-height:1.55;">
                        Recibimos una solicitud para restablecer la contraseña de tu usuario
                        <strong>{{ $usuario }}</strong> en el sistema KUSI.
                    </p>
                    <p style="margin:0 0 6px;line-height:1.55;">Pulsa el botón para elegir una contraseña nueva:</p>
                    <p style="text-align:center;margin:26px 0;">
                        <a href="{{ $url }}" style="background:#6A3FA3;color:#ffffff;text-decoration:none;padding:14px 30px;border-radius:12px;font-weight:bold;display:inline-block;">
                            Restablecer mi contraseña
                        </a>
                    </p>
                    <p style="margin:0 0 12px;font-size:13px;color:#7B6F8F;line-height:1.5;">
                        Este enlace vence en <strong>{{ $minutos }} minutos</strong> y solo se puede usar una vez.
                    </p>
                    <p style="margin:0 0 12px;font-size:13px;color:#7B6F8F;line-height:1.5;">
                        Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                        <a href="{{ $url }}" style="color:#6A3FA3;word-break:break-all;">{{ $url }}</a>
                    </p>
                    <p style="margin:18px 0 0;font-size:13px;color:#7B6F8F;line-height:1.5;">
                        Si tú no pediste este cambio, ignora este mensaje: tu contraseña seguirá igual.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="background:#F4ECFC;padding:16px 28px;font-size:12px;color:#7B6F8F;text-align:center;">
                    © {{ date('Y') }} Sociedad de Apoyo Social KUSI
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
