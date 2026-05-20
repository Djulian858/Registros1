<?php
$host = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'trabajo';
$port = 3306;

$conexion = new mysqli($host, $user, $password, $database, $port);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error . ". Verifica que MySQL/MariaDB esté arrancado en XAMPP y que el puerto 3306 esté disponible.");
}
?>