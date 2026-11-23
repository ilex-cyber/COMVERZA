@extends('emails.layout')

@section('contenido')
    <h1 style="margin:0 0 14px;font-size:22px;color:#06112f;">{{ $titulo }}</h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#34476d;">{!! nl2br(e($intro)) !!}</p>

    @if (count($filas))
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e6edf8;border-radius:12px;border-collapse:separate;margin-bottom:22px;">
            @foreach ($filas as [$etiqueta, $valor])
                <tr>
                    <td style="padding:11px 16px;font-size:13px;color:#7183a0;width:38%;border-bottom:1px solid #f0f4fa;">{{ $etiqueta }}</td>
                    <td style="padding:11px 16px;font-size:14px;color:#172d59;font-weight:bold;border-bottom:1px solid #f0f4fa;">{!! nl2br(e($valor)) !!}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($botonTexto && $botonUrl)
        <p style="margin:6px 0 26px;">
            <a href="{{ $botonUrl }}" style="display:inline-block;background:#ffd533;color:#06112f;font-weight:bold;padding:13px 26px;border-radius:10px;text-decoration:none;">{{ $botonTexto }} →</a>
        </p>
    @endif
@endsection
