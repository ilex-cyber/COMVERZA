@extends('layouts.panel')
@section('title', 'Consola API')

@section('content')
<x-encabezado eyebrow="API REST · JavaScript" titulo="Consola de la API de Categorías" subtitulo="Consumo de los cuatro métodos (GET, POST, PUT, DELETE) con fetch() de JavaScript sobre /api/v1/categorias." />

<div class="grid grid-2">
    <div class="grid" style="align-content:start">
        <div class="card card-pad">
            <h3 style="color:var(--navy);margin-bottom:12px"><span class="method m-get">GET</span> Listar todas / obtener una</h3>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <button class="btn btn-outline" id="btnGetAll">GET /categorias</button>
                <input class="input" id="getId" type="number" min="1" placeholder="ID" style="max-width:100px">
                <button class="btn btn-outline" id="btnGetOne">GET /categorias/{id}</button>
            </div>
        </div>
        <div class="card card-pad">
            <h3 style="color:var(--navy);margin-bottom:12px"><span class="method m-post">POST</span> Crear · <span class="method m-put">PUT</span> Actualizar</h3>
            <div class="form-grid">
                <div class="field"><label>ID (solo para PUT)</label><input id="putId" type="number" min="1"></div>
                <div class="field"><label>Nombre</label><input id="nombre" type="text" value="Categoría API {{ now()->format('His') }}"></div>
                <div class="field full"><label>Descripción</label><input id="descripcion" type="text" value="Creada desde la consola JavaScript"></div>
                <div class="field"><label>Color</label><input id="color" type="color" value="#784cff"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:14px">
                <button class="btn btn-yellow" id="btnPost">POST crear</button>
                <button class="btn btn-blue" id="btnPut">PUT actualizar</button>
            </div>
        </div>
        <div class="card card-pad">
            <h3 style="color:var(--navy);margin-bottom:12px"><span class="method m-delete">DELETE</span> Eliminar</h3>
            <div style="display:flex;gap:8px">
                <input class="input" id="delId" type="number" min="1" placeholder="ID" style="max-width:120px">
                <button class="btn btn-danger" id="btnDelete">DELETE /categorias/{id}</button>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-head"><h3>Respuesta</h3><span id="estado" class="badge badge-gray">Sin petición</span></div>
        <div class="card-pad"><div class="code" id="salida">Pulsa un botón para consumir la API…</div></div>
        <div class="card-head" style="border-top:1px solid var(--line)"><h3>Tabla en vivo</h3><small id="total"></small></div>
        <div class="table-wrap" style="max-height:330px;overflow:auto">
            <table class="table"><thead><tr><th>ID</th><th>Nombre</th><th class="num">Productos</th></tr></thead><tbody id="tabla"></tbody></table>
        </div>
    </div>
</div>

<div class="card card-pad" style="margin-top:20px">
    <h3 style="color:var(--navy);margin-bottom:10px">Código utilizado</h3>
    <div class="code" id="codigo"></div>
</div>
@endsection

@push('scripts')
<script id="apiClient">
// ===== Cliente JavaScript para la API de COMVERZA =====
const API = '{{ url('/api/v1/categorias') }}';

async function llamar(metodo, url, cuerpo) {
    const opciones = { method: metodo, headers: { 'Accept': 'application/json' } };
    if (cuerpo) {
        opciones.headers['Content-Type'] = 'application/json';
        opciones.body = JSON.stringify(cuerpo);
    }
    const inicio = performance.now();
    const res = await fetch(url, opciones);
    const datos = await res.json();
    return { status: res.status, ms: Math.round(performance.now() - inicio), datos };
}

const obtenerTodas = () => llamar('GET', API);
const obtenerUna = id => llamar('GET', `${API}/${id}`);
const crear = categoria => llamar('POST', API, categoria);
const actualizar = (id, categoria) => llamar('PUT', `${API}/${id}`, categoria);
const eliminar = id => llamar('DELETE', `${API}/${id}`);
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    $('codigo').textContent = $('apiClient').textContent.trim();

    function mostrar(etiqueta, r) {
        const ok = r.status < 300;
        $('estado').className = 'badge ' + (ok ? 'badge-green' : 'badge-red');
        $('estado').textContent = `${etiqueta} → ${r.status} · ${r.ms} ms`;
        $('salida').textContent = JSON.stringify(r.datos, null, 2);
    }
    async function refrescar() {
        const r = await obtenerTodas();
        $('total').textContent = r.datos.total + ' registros';
        $('tabla').innerHTML = r.datos.data.map(c =>
            `<tr><td>${c.id}</td><td><span class="dot" style="background:${c.color}"></span>${c.nombre}</td><td class="num">${c.productos_count}</td></tr>`).join('');
    }
    const cuerpo = () => ({ nombre: $('nombre').value, descripcion: $('descripcion').value, color: $('color').value, activo: true });

    $('btnGetAll').onclick = async () => { mostrar('GET all', await obtenerTodas()); refrescar(); };
    $('btnGetOne').onclick = async () => mostrar('GET one', await obtenerUna($('getId').value || 1));
    $('btnPost').onclick = async () => {
        const r = await crear(cuerpo()); mostrar('POST', r);
        if (r.datos.data) { $('putId').value = $('delId').value = $('getId').value = r.datos.data.id; }
        refrescar();
    };
    $('btnPut').onclick = async () => {
        if (!$('putId').value) return Swal.fire({ icon: 'info', text: 'Escribe el ID a actualizar.', confirmButtonColor: '#1677ff' });
        mostrar('PUT', await actualizar($('putId').value, cuerpo())); refrescar();
    };
    $('btnDelete').onclick = async () => {
        if (!$('delId').value) return Swal.fire({ icon: 'info', text: 'Escribe el ID a eliminar.', confirmButtonColor: '#1677ff' });
        const ok = await Swal.fire({ icon: 'warning', title: `¿Eliminar la categoría ${$('delId').value}?`, showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#f04438' });
        if (ok.isConfirmed) { mostrar('DELETE', await eliminar($('delId').value)); refrescar(); }
    };
    refrescar();
});
</script>
@endpush
