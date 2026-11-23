@extends('layouts.panel')
@section('title', 'Gráficas')

@section('content')
<x-encabezado eyebrow="Creación manual de gráficas" titulo="Constructor de gráficas" subtitulo="Captura tus propias etiquetas y valores, elige el tipo de gráfica y descárgala como imagen. También puedes cargar datos reales del sistema." />

<div class="grid grid-3">
    <div class="card card-pad">
        <form id="formGrafica" class="js-validate" onsubmit="return false">
            <div class="field" style="margin-bottom:16px">
                <label for="g_titulo">Título <span class="req">*</span></label>
                <input id="g_titulo" type="text" required maxlength="80" value="Ventas trimestrales" data-nombre="El título">
            </div>
            <div class="field" style="margin-bottom:16px">
                <label for="g_tipo">Tipo de gráfica <span class="req">*</span></label>
                <select id="g_tipo" required data-nombre="El tipo">
                    <option value="bar">Barras</option>
                    <option value="line">Líneas</option>
                    <option value="pie">Pastel</option>
                    <option value="doughnut">Dona</option>
                    <option value="radar">Radar</option>
                    <option value="polarArea">Área polar</option>
                </select>
            </div>
            <div class="field" style="margin-bottom:16px">
                <label for="g_etiquetas">Etiquetas (separadas por coma) <span class="req">*</span></label>
                <input id="g_etiquetas" type="text" required value="T1, T2, T3, T4" data-nombre="Las etiquetas">
            </div>
            <div class="field" style="margin-bottom:16px">
                <label for="g_valores">Valores numéricos (separados por coma) <span class="req">*</span></label>
                <input id="g_valores" type="text" required value="120000, 185000, 160000, 240000" pattern="^\s*-?\d+(\.\d+)?(\s*,\s*-?\d+(\.\d+)?)*\s*$"
                       data-nombre="Los valores" data-patron="Solo números separados por coma (ej. 10, 20.5, 30).">
                <div class="hint">Debe haber el mismo número de valores que de etiquetas.</div>
            </div>
            <div class="field" style="margin-bottom:16px">
                <label for="g_color">Color principal</label>
                <input id="g_color" type="color" value="#1677ff">
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <button class="btn btn-yellow" id="btnGraficar" type="button"><i data-lucide="chart-column"></i> Graficar</button>
                <button class="btn btn-outline" id="btnDescargar" type="button"><i data-lucide="download"></i> PNG</button>
            </div>
            <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
            <label style="font-weight:700;font-size:13px">Cargar datos reales</label>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px">
                @foreach ($presets as $k => $p)
                    <button class="btn btn-outline btn-sm" type="button" data-preset="{{ $k }}">{{ $p['titulo'] }}</button>
                @endforeach
            </div>
        </form>
    </div>
    <div class="card span-2">
        <div class="card-head"><h3 id="tituloGrafica">Vista previa</h3><small>Chart.js 4</small></div>
        <div class="chart-box lg"><canvas id="lienzo"></canvas></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const presets = @json($presets);
    const $ = id => document.getElementById(id);
    let chart;
    const paleta = base => ['#1677ff', '#784cff', '#ffd533', '#35a7ff', '#12b76a', '#ff6b4a', '#0fb5a6', '#06112f', '#a0632b', '#f79009', '#2b3f8f', '#ffbd1c'].map((c, i) => i === 0 ? base : c);

    function graficar() {
        const form = $('formGrafica');
        const etiquetas = $('g_etiquetas').value.split(',').map(s => s.trim()).filter(Boolean);
        const valores = $('g_valores').value.split(',').map(s => s.trim()).filter(Boolean);
        $('g_valores').setCustomValidity(etiquetas.length !== valores.length ? `Hay ${etiquetas.length} etiquetas y ${valores.length} valores; deben coincidir.` : '');
        if (!form.reportValidity()) {
            const msg = $('g_valores').validationMessage || 'Revisa los datos capturados.';
            Swal.fire({ icon: 'error', title: 'Datos inválidos', text: msg, confirmButtonColor: '#1677ff' });
            return;
        }
        const tipo = $('g_tipo').value, color = $('g_color').value;
        const circular = ['pie', 'doughnut', 'polarArea'].includes(tipo);
        $('tituloGrafica').textContent = $('g_titulo').value;
        chart && chart.destroy();
        chart = new Chart($('lienzo'), {
            type: tipo,
            data: { labels: etiquetas, datasets: [{
                label: $('g_titulo').value, data: valores.map(Number),
                backgroundColor: circular ? paleta(color) : (tipo === 'line' || tipo === 'radar' ? color + '33' : color),
                borderColor: circular ? '#fff' : color, borderWidth: 2, borderRadius: tipo === 'bar' ? 8 : 0, tension: .35, fill: tipo !== 'bar',
            }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: circular, position: 'bottom' }, title: { display: true, text: $('g_titulo').value, color: '#06112f', font: { size: 16 } } } },
        });
    }

    $('btnGraficar').addEventListener('click', graficar);
    $('btnDescargar').addEventListener('click', () => {
        if (!chart) return;
        const a = document.createElement('a');
        a.href = chart.toBase64Image('image/png', 1); a.download = ($('g_titulo').value || 'grafica') + '.png'; a.click();
    });
    document.querySelectorAll('[data-preset]').forEach(b => b.addEventListener('click', () => {
        const p = presets[b.dataset.preset];
        $('g_titulo').value = p.titulo; $('g_tipo').value = p.tipo;
        $('g_etiquetas').value = p.etiquetas.join(', '); $('g_valores').value = p.valores.join(', ');
        graficar();
    }));
    graficar();
});
</script>
@endpush
