<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMVERZA</title>
</head>
<body style="margin:0;padding:0;background:#eef3fb;font-family:Arial,Helvetica,sans-serif;color:#172d59;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eef3fb;padding:32px 12px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 40px rgba(6,17,47,.08);">
                <tr>
                    <td style="background:#06112f;padding:26px 32px;border-bottom:4px solid #ffd533;">
                        <span style="font-size:24px;font-weight:bold;color:#ffffff;letter-spacing:.5px;">COMVER<span style="color:#ffd533;">ZA</span></span>
                        <div style="font-size:12px;color:#8fa4c6;margin-top:4px;">Business Platform</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:34px 32px 10px;">
                        @yield('contenido')
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 32px 30px;font-size:12px;color:#7183a0;border-top:1px solid #e6edf8;">
                        Este correo fue enviado automáticamente por COMVERZA. Si no esperabas este mensaje, puedes ignorarlo.<br>
                        © {{ date('Y') }} COMVERZA · Tecnología que impulsa tu negocio.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
