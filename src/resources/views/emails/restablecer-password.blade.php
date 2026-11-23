@extends('emails.layout')

@section('contenido')
    <h1 style="margin:0 0 14px;font-size:22px;color:#06112f;">Hola, {{ $nombre }}</h1>
    <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#34476d;">
        Recibimos una solicitud para restablecer la contraseña de tu cuenta en COMVERZA.
        Haz clic en el botón para crear una nueva contraseña.
    </p>
    <p style="margin:6px 0 24px;">
        <a href="{{ $url }}" style="display:inline-block;background:#ffd533;color:#06112f;font-weight:bold;padding:13px 26px;border-radius:10px;text-decoration:none;">Restablecer contraseña →</a>
    </p>
    <p style="margin:0 0 10px;font-size:13px;line-height:1.6;color:#7183a0;">
        El enlace vence en {{ $minutos }} minutos. Si tú no lo solicitaste, ignora este correo: tu contraseña actual seguirá funcionando.
    </p>
    <p style="margin:0 0 20px;font-size:12px;color:#9aabc6;word-break:break-all;">
        Si el botón no funciona, copia este enlace en tu navegador:<br>{{ $url }}
    </p>
@endsection
