@extends('layouts.app')

@section('content')

<h2>Registros de Préstamos</h2>

@if(session('mensaje') === 'actualizado')
    <div class="alert alert--success"><p class="alert__title">✅ Registro actualizado correctamente</p></div>
@elseif(session('mensaje') === 'eliminado')
    <div class="alert alert--warning"><p class="alert__title">✅ Registro eliminado correctamente</p></div>
@elseif(session('mensaje') && str_starts_with(session('mensaje'), 'actualizado_con_error'))
    <div class="alert alert--warning"><p class="alert__title">⚠️ {{ session('mensaje') }}</p></div>
@endif

<div style="padding: 0 20px 20px;">

@if($prestamos->isEmpty())
    <p class="no-registros">No hay registros aún</p>
@else
    @foreach($prestamos as $prestamo)
    <details class="registro-card">
        <summary>
            <div class="registro-summary">
                <span>👤 <strong>{{ $prestamo->colaborador }}</strong></span>
                <span>💻 {{ $prestamo->tipo_equipo }} / {{ $prestamo->modelo_equipo }}</span>
                <span>📅 {{ $prestamo->fecha_entrega }}</span>
                <span>🔄 {{ $prestamo->fecha_devolucion ?? 'Pendiente' }}</span>
            </div>
            <span class="registro-chevron">▼</span>
        </summary>

        <div class="registro-contenido">
            <h3>Datos de Préstamo</h3>
            <table class="tabla-registro-detalle">
                <tr><td><strong>Colaborador</strong></td><td>{{ $prestamo->colaborador }}</td></tr>
                <tr><td><strong>Correo</strong></td><td>{{ $prestamo->correo_colaborador }}</td></tr>
                <tr><td><strong>Serial</strong></td><td>{{ $prestamo->serial_pc }}</td></tr>
                <tr><td><strong>Entrega</strong></td><td>{{ $prestamo->fecha_entrega }} - {{ $prestamo->hora_entrega }}</td></tr>
                <tr><td><strong>Entregado por</strong></td><td>{{ $prestamo->entregado_por }}</td></tr>
                <tr><td><strong>Observaciones</strong></td><td>{{ $prestamo->observaciones_prestamo ?: '-' }}</td></tr>
            </table>

            <h3>Datos de Devolución</h3>
            <table class="tabla-registro-detalle">
                <tr>
                    <td><strong>Fecha</strong></td>
                    <td>{{ $prestamo->fecha_devolucion ?? '<em style="color:#6b7280;">Pendiente</em>' }}</td>
                </tr>
                <tr>
                    <td><strong>Hora</strong></td>
                    <td>{{ $prestamo->hora_devolucion ?? '<em style="color:#6b7280;">Pendiente</em>' }}</td>
                </tr>
                <tr><td><strong>Observaciones</strong></td><td>{{ $prestamo->observaciones_devolucion ?: '-' }}</td></tr>
            </table>

            <div id="editar-{{ $prestamo->id }}" style="display: none; background-color: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-top: 10px;">
                <form action="{{ route('prestamos.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" value="{{ $prestamo->id }}">

                    <div class="form-group">
                        <label for="fecha_dev_{{ $prestamo->id }}">Fecha Devolución:</label>
                        <input type="date" id="fecha_dev_{{ $prestamo->id }}" name="fecha_devolucion"
                               value="{{ $prestamo->fecha_devolucion }}" class="small-input">
                    </div>

                    <div class="form-group">
                        <label for="hora_dev_{{ $prestamo->id }}">Hora Devolución:</label>
                        <input type="time" id="hora_dev_{{ $prestamo->id }}" name="hora_devolucion"
                               value="{{ $prestamo->hora_devolucion }}" class="small-input">
                    </div>

                    <div class="form-group">
                        <label for="obs_dev_{{ $prestamo->id }}">Observaciones (Devolución):</label>
                        <textarea id="obs_dev_{{ $prestamo->id }}" name="observaciones_devolucion"
                                  placeholder="Comentarios sobre el estado del equipo"
                                  class="small-textarea">{{ $prestamo->observaciones_devolucion }}</textarea>
                    </div>

                    <div class="registro-acciones">
                        <button type="submit" class="btn-guardar">✅ Guardar Cambios</button>
                        <button type="button" onclick="toggleEditar({{ $prestamo->id }})" class="btn-cancelar">❌ Cancelar</button>
                    </div>
                </form>
            </div>

            <div class="registro-acciones">
                <button type="button" onclick="toggleEditar({{ $prestamo->id }})"
                        id="btn-editar-{{ $prestamo->id }}" class="btn-editar">✏️ Editar Devolución</button>
                <button type="button" onclick="eliminarRegistro({{ $prestamo->id }})" class="btn-eliminar">🗑️ Eliminar</button>
            </div>
        </div>
    </details>
    @endforeach
@endif

</div>

<a href="{{ route('prestamos.index') }}" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #0275d8; color: white; text-decoration: none; border-radius: 4px;">← Nuevo Registro</a>

<script>
function toggleEditar(id) {
    const editar = document.getElementById('editar-' + id);
    const btnEditar = document.getElementById('btn-editar-' + id);
    if (editar.style.display === 'none') {
        editar.style.display = 'block';
        btnEditar.textContent = '❌ Cancelar Edición';
    } else {
        editar.style.display = 'none';
        btnEditar.textContent = '✏️ Editar Devolución';
    }
}

function eliminarRegistro(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
        fetch('/prestamos/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
                    document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        }).then(() => window.location.href = '{{ route("prestamos.ver") }}?mensaje=eliminado');
    }
}
</script>

@endsection
