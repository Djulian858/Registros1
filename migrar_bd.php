<?php
include("conexion.php");

// Verificar y actualizar estructura de la tabla prestamos

echo "<h2>Verificación y Migración de Base de Datos</h2>";

// Obtener información sobre la tabla
$resultado = $conexion->query("DESCRIBE prestamos");

$columnas = [];
while ($fila = $resultado->fetch_assoc()) {
    $columnas[] = $fila['Field'];
}

echo "<h3>Columnas actuales:</h3>";
echo "<pre>";
print_r($columnas);
echo "</pre>";

// Mapeo de cambios necesarios
$cambios_necesarios = [];

// Cambios de nombres
$renombramientos = [
    'docente' => 'colaborador',
    'correo_docente' => 'correo_colaborador',
    'recibido_por' => 'entregado_por'
];

// Campos a agregar
$campos_agregar = [
    'modelo_equipo' => 'VARCHAR(255) DEFAULT NULL',
    'fecha_devolucion' => 'DATE DEFAULT NULL',
    'observaciones_prestamo' => 'TEXT DEFAULT NULL',
    'observaciones_devolucion' => 'TEXT DEFAULT NULL'
];

// Campos a eliminar
$campos_eliminar = ['firma_docente', 'correo_recibido'];

// Detectar cambios necesarios
foreach ($renombramientos as $viejo => $nuevo) {
    if (in_array($viejo, $columnas) && !in_array($nuevo, $columnas)) {
        $cambios_necesarios[] = "ALTER TABLE prestamos CHANGE COLUMN $viejo $nuevo VARCHAR(255)";
    }
}

foreach ($campos_agregar as $campo => $tipo) {
    if (!in_array($campo, $columnas)) {
        $cambios_necesarios[] = "ALTER TABLE prestamos ADD COLUMN $campo $tipo";
    }
}

foreach ($campos_eliminar as $campo) {
    if (in_array($campo, $columnas)) {
        $cambios_necesarios[] = "ALTER TABLE prestamos DROP COLUMN $campo";
    }
}

if (empty($cambios_necesarios)) {
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3 style='color: #155724;'>✅ Base de datos actualizada correctamente</h3>";
    echo "<p>La tabla prestamos tiene la estructura correcta.</p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3 style='color: #856404;'>⚠️ Cambios necesarios detectados</h3>";
    echo "<p>Se ejecutarán los siguientes cambios:</p>";
    echo "<pre>";
    foreach ($cambios_necesarios as $sql) {
        echo $sql . ";\n";
    }
    echo "</pre>";
    
    // Ejecutar cambios
    echo "<h3>Ejecutando cambios...</h3>";
    $todos_ok = true;
    foreach ($cambios_necesarios as $sql) {
        if ($conexion->query($sql)) {
            echo "<p style='color: green;'>✅ " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p style='color: red;'>❌ Error: " . $conexion->error . "</p>";
            $todos_ok = false;
        }
    }
    
    if ($todos_ok) {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
        echo "<h3 style='color: #155724;'>✅ Migración completada exitosamente</h3>";
        echo "<p><a href='index.php'>Ir al formulario</a></p>";
        echo "</div>";
    }
    echo "</div>";
}

// Manejo de campos observaciones antiguos
$check_obs = $conexion->query("SHOW COLUMNS FROM prestamos LIKE 'observaciones'");
if ($check_obs && $check_obs->num_rows > 0) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3 style='color: #721c24;'>⚠️ Campo observaciones antiguo detectado</h3>";
    echo "<p>Se migrará el contenido a observaciones_prestamo...</p>";
    
    $conexion->query("UPDATE prestamos SET observaciones_prestamo = observaciones WHERE observaciones_prestamo IS NULL AND observaciones IS NOT NULL");
    $conexion->query("ALTER TABLE prestamos DROP COLUMN observaciones");
    
    echo "<p style='color: green;'>✅ Migración completada</p>";
    echo "</div>";
}

$conexion->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    pre {
        background-color: #f4f4f4;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
</style>
