<?php
// Configuración de conexión: Detecta automáticamente si es Local o Producción

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == '::1') {
    // --- ENTORNO LOCAL (XAMPP) ---
    $host     = "localhost";
    $usuario  = "root";
    $password = ""; 
    $db_name  = "fuego_dragon_2.0";
} else {
    // --- ENTORNO PRODUCCIÓN (Hosting) ---
    // NOTA: En cPanel, el nombre de la BD y el usuario suelen llevar un prefijo (ej: tuusuario_nombre)
    $host     = "sql301.byethost10.com"; // Host específico de TonoHost
    $usuario  = "b10_40802369";
    $password = "HHolger19*"; // Es la misma contraseña que usas para entrar a tu cuenta de TonoHost
    $db_name  = "b10_40802369_FuegoDragon";
}

// 1. Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $db_name);

// 2. Verificar si hay errores
if ($conexion->connect_error) {
    // En producción es mejor no mostrar el error técnico detallado por seguridad
    $msg_error = ($_SERVER['SERVER_NAME'] == 'localhost') ? $conexion->connect_error : "Error al conectar con la base de datos.";
    die("❌ " . $msg_error);
}

//echo "✅ ¡Conexión exitosa a la base de datos Fuego-Dragon 2.0!";

// 3. (Opcional) Verificar el juego de caracteres para tildes y ñ
$conexion->set_charset("utf8mb4");

// 4. Cerrar la conexión (buena práctica al terminar la prueba)
//$conexion->close();
?>