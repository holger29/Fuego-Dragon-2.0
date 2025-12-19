<?php
// Configuración de las credenciales (Valores por defecto de XAMPP)
$host     = "localhost";
$usuario  = "root";
$password = ""; // En XAMPP el root no tiene contraseña por defecto
$db_name  = "fuego_dragon_2.0";

// 1. Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $db_name);

// 2. Verificar si hay errores
if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error);
}

//echo "✅ ¡Conexión exitosa a la base de datos Fuego-Dragon 2.0!";

// 3. (Opcional) Verificar el juego de caracteres para tildes y ñ
$conexion->set_charset("utf8mb4");

// 4. Cerrar la conexión (buena práctica al terminar la prueba)
//$conexion->close();
?>