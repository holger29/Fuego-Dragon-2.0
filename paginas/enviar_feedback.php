<?php
// 1. Iniciamos la sesión para poder identificar al usuario logueado
session_start();

// 2. Incluimos la conexión a la base de datos (asegúrate que la ruta sea correcta)
include("../conexion/conexion.php");

// 3. Verificamos que la petición sea POST y que el usuario tenga una sesión activa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['usuario_id'])) {
    
    // Obtenemos el ID del usuario desde la sesión
    $id_usuario = $_SESSION['usuario_id'];
    
    // Obtenemos el comentario y eliminamos espacios en blanco al inicio y final
    $comentario = trim($_POST['comentario']);

    // 4. Validamos que el comentario no esté vacío antes de intentar guardarlo
    if (!empty($comentario)) {
        
        // Preparamos la sentencia SQL. 
        // Usamos NOW() para la fecha y el valor 0 para la columna 'leido' por defecto
        $sql = "INSERT INTO feedback (usuario_id, mensaje, fecha_envio, leido) VALUES (?, ?, NOW(), 0)";
        
        $stmt = $conexion->prepare($sql);
        
        if ($stmt) {
            // "is" significa que el primer parámetro es Integer y el segundo String
            $stmt->bind_param("is", $id_usuario, $comentario);
            
            // 5. Ejecutamos la inserción
            if ($stmt->execute()) {
                // Si se guarda con éxito, usamos JavaScript para el mensaje y redireccionamos de inmediato
                echo "<script>
                    alert('¡Gracias por tu feedback! Tus comentarios son importantes para nosotros y nos ayudan a mejorar.');
                    window.location.href='Perfil.php';
                </script>";
                exit(); // Detenemos el script para asegurar la redirección
            } else {
                // En caso de error en la base de datos
                echo "Error al guardar el comentario: " . $conexion->error;
            }
            
            // Cerramos la sentencia preparada
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conexion->error;
        }
    } else {
        // Si el comentario estaba vacío, simplemente regresamos al perfil
        echo "<script>
            alert('Por favor, escribe un mensaje antes de enviar.');
            window.location.href='Perfil.php';
        </script>";
        exit();
    }
} else {
    // Si se intenta acceder al archivo sin una sesión o sin enviar el formulario
    header("Location: Perfil.php");
    exit();
}

// 6. Cerramos la conexión a la base de datos al finalizar
$conexion->close();
?>