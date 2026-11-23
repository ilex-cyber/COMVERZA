@props(['action', 'nombre', 'mensaje' => null, 'texto' => null])
<form method="POST" action="{{ $action }}" class="js-confirm" style="display:inline"
      data-titulo="¿Eliminar «{{ $nombre }}»?"
      data-mensaje="{{ $mensaje ?? 'Esta acción no se puede deshacer.' }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger {{ $texto ? 'btn-sm' : 'btn-icon' }}" title="Eliminar">
        <i data-lucide="trash-2"></i>{{ $texto }}
    </button>
</form>
