<?php
include("conexion.php");

// Validar que los datos requeridos existan
if (!isset($_GET['id'])) {
    die("❌ Error: ID no proporcionado <br><a href='ver.php'>Volver</a>");
}

$id = intval($_GET['id']);

// Confirmar que existe antes de eliminar
$verificar = $conexion->query("SELECT id FROM prestamos WHERE id = " . $id);

if ($verificar->num_rows == 0) {
    die("❌ Error: Registro no encontrado <br><a href='ver.php'>Volver</a>");
}

// Eliminar el registro
$sql = "DELETE FROM prestamos WHERE id = " . $id;

if ($conexion->query($sql) === TRUE) {
    header("Location: ver.php?mensaje=eliminado");
    exit();
} else {
    echo "❌ Error al eliminar: " . $conexion->error . " <br><a href='ver.php'>Volver</a>";
}

$conexion->close();
?>
