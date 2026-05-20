<?php include("includes/header.php"); ?>
<?php include("conexion.php"); ?>

<h2>Registros de Préstamos</h2>

<?php
// Mostrar mensajes
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    if ($mensaje == 'actualizado') {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;'>
            ✅ Registro actualizado correctamente
        </div>";
    } elseif ($mensaje == 'eliminado') {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;'>
            ✅ Registro eliminado correctamente
        </div>";
    }
}
?>

<div style="padding: 0 20px 20px;">

<?php
$resultado = $conexion->query("SELECT * FROM prestamos ORDER BY id DESC");

if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()){
        $id = $fila['id'];
        $observaciones_prestamo = !empty($fila['observaciones_prestamo']) ? $fila['observaciones_prestamo'] : '-';
        $observaciones_devolucion = !empty($fila['observaciones_devolucion']) ? $fila['observaciones_devolucion'] : '-';
        ?>
        
        <details class="registro-card">
            <summary>
                <div class="registro-summary">
                    <span>👤 <strong><?php echo htmlspecialchars($fila['colaborador']); ?></strong></span>
                    <span>💻 <?php echo htmlspecialchars($fila['tipo_equipo']); ?> / <?php echo htmlspecialchars($fila['modelo_equipo']); ?></span>
                    <span>📅 <?php echo htmlspecialchars($fila['fecha_entrega']); ?></span>
                    <span>🔄 <?php echo !empty($fila['fecha_devolucion']) ? htmlspecialchars($fila['fecha_devolucion']) : 'Pendiente'; ?></span>
                </div>
                <span class="registro-chevron">▼</span>
            </summary>

            <div class="registro-contenido">
                <h3>Datos de Préstamo</h3>
                <table class="tabla-registro-detalle">
                    <tr>
                        <td><strong>Colaborador</strong></td>
                        <td><?php echo htmlspecialchars($fila['colaborador']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Correo</strong></td>
                        <td><?php echo htmlspecialchars($fila['correo_colaborador']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Serial</strong></td>
                        <td><?php echo htmlspecialchars($fila['serial_pc']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Entrega</strong></td>
                        <td><?php echo htmlspecialchars($fila['fecha_entrega']); ?> - <?php echo htmlspecialchars($fila['hora_entrega']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Entregado por</strong></td>
                        <td><?php echo htmlspecialchars($fila['entregado_por']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Observaciones</strong></td>
                        <td><?php echo $observaciones_prestamo; ?></td>
                    </tr>
                </table>

                <h3>Datos de Devolución</h3>
                <table class="tabla-registro-detalle">
                    <tr>
                        <td><strong>Fecha</strong></td>
                        <td><?php echo !empty($fila['fecha_devolucion']) ? htmlspecialchars($fila['fecha_devolucion']) : '<em style="color: #6b7280;">Pendiente</em>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Hora</strong></td>
                        <td><?php echo !empty($fila['hora_devolucion']) ? htmlspecialchars($fila['hora_devolucion']) : '<em style="color: #6b7280;">Pendiente</em>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Observaciones</strong></td>
                        <td><?php echo $observaciones_devolucion; ?></td>
                    </tr>
                </table>

                <div id="editar-<?php echo $id; ?>" style="display: none; background-color: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-top: 10px;">
                    <form action="editar.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="form-group">
                            <label for="fecha_dev_<?php echo $id; ?>">Fecha Devolución:</label>
                            <input type="date" id="fecha_dev_<?php echo $id; ?>" name="fecha_devolucion" value="<?php echo htmlspecialchars($fila['fecha_devolucion']); ?>" class="small-input">
                        </div>

                        <div class="form-group">
                            <label for="hora_dev_<?php echo $id; ?>">Hora Devolución:</label>
                            <input type="time" id="hora_dev_<?php echo $id; ?>" name="hora_devolucion" value="<?php echo htmlspecialchars($fila['hora_devolucion']); ?>" class="small-input">
                        </div>

                        <div class="form-group">
                            <label for="obs_dev_<?php echo $id; ?>">Observaciones (Devolución):</label>
                            <textarea id="obs_dev_<?php echo $id; ?>" name="observaciones_devolucion" placeholder="Comentarios sobre el estado del equipo" class="small-textarea"><?php echo htmlspecialchars($fila['observaciones_devolucion']); ?></textarea>
                        </div>

                        <div class="registro-acciones">
                            <button type="submit" class="btn-guardar">✅ Guardar Cambios</button>
                            <button type="button" onclick="toggleEditar(<?php echo $id; ?>)" class="btn-cancelar">❌ Cancelar</button>
                        </div>
                    </form>
                </div>

                <div class="registro-acciones">
                    <button type="button" onclick="toggleEditar(<?php echo $id; ?>)" id="btn-editar-<?php echo $id; ?>" class="btn-editar">✏️ Editar Devolución</button>
                    <button type="button" onclick="eliminarRegistro(<?php echo $id; ?>)" class="btn-eliminar">🗑️ Eliminar</button>
                </div>
            </div>
        </details>

        <?php
    }
} else {
    echo "<p style='text-align: center; color: #999;'>No hay registros aún</p>";
}

$conexion->close();
?>

</div>

<a href="index.php" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #0275d8; color: white; text-decoration: none; border-radius: 4px;">← Nuevo Registro</a>

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
        window.location.href = 'eliminar.php?id=' + id;
    }
}
</script>

<?php include("includes/footer.php"); ?>