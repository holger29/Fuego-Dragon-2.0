<?php
session_start();
include("../conexion/conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../autenticacion/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['usuario_id'];
    
    // Recibir y limpiar los datos del formulario
    $nombre  = trim($_POST['nuevo_nombre']);
    $email   = trim($_POST['nuevo_email']);
    $pais    = trim($_POST['nuevo_pais']);
    $ciudad  = trim($_POST['nueva_ciudad']);
    $celular = trim($_POST['nuevo_celular']);

    /* Por seguridad para que no se guarden campos vacíos vamos
    a ejecutar este código antes de UPDATE:*/
if (empty($_POST['nuevo_nombre']) || empty($_POST['nuevo_email'])) {
    echo "<script>
            alert('Error: El nombre y el email no pueden estar vacíos.');
            window.location.href = 'perfil.php';
          </script>";
    exit(); // Detiene la ejecución para no borrar los datos en la BD
}

    // Sentencia SQL para actualizar
    $sql = "UPDATE usuarios SET 
            nombre_completo = ?, 
            email = ?, 
            pais_residencia = ?, 
            ciudad_residencia = ?, 
            celular = ? 
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $email, $pais, $ciudad, $celular, $id_usuario);

    if ($stmt->execute()) {
        // Actualizar el nombre en la sesión por si cambió
        $_SESSION['usuario_nombre'] = $nombre;
        
        echo "<script>
                alert('¡Datos actualizados con éxito!');
                window.location.href = 'perfil.php';
              </script>";
    } else {
        echo "Error al actualizar los datos: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
?>