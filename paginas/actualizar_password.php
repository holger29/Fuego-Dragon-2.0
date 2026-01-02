<?php
session_start();
include("../conexion/conexion.php");

// 1. Protección de acceso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../autenticacion/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['usuario_id'];
    $pass_actual  = $_POST['current_password'];
    $pass_nueva   = $_POST['new_password'];
    $pass_confirm = $_POST['confirm_new_password'];

    // 2. Validar que los campos no estén vacíos
    if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirm)) {
        echo "<script>alert('Todos los campos son obligatorios'); window.location.href='perfil.php';</script>";
        exit();
    }

    // Validar complejidad de contraseña (Min 8 chars, 1 Mayúscula, 1 Número)
    if (strlen($pass_nueva) < 8 || !preg_match('/[A-Z]/', $pass_nueva) || !preg_match('/[0-9]/', $pass_nueva)) {
        echo "<script>alert('La contraseña debe tener al menos 8 caracteres, una mayúscula y un número'); window.location.href='perfil.php';</script>";
        exit();
    }

    // 3. Obtener el hash de la contraseña actual de la BD
    $sql = "SELECT contrasena FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    // 4. Verificar la contraseña actual
    if (password_verify($pass_actual, $usuario['contrasena'])) {
        
        // 5. Verificar que la nueva y la confirmación coincidan
        if ($pass_nueva === $pass_confirm) {
            
            // 6. Cifrar la nueva contraseña
            $nuevo_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
            
            $update_sql = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
            $update_stmt = $conexion->prepare($update_sql);
            $update_stmt->bind_param("si", $nuevo_hash, $id_usuario);
            
            if ($update_stmt->execute()) {
                echo "<script>alert('¡Contraseña actualizada con éxito!'); window.location.href='perfil.php';</script>";
            } else {
                echo "Error al actualizar.";
            }
        } else {
            echo "<script>alert('La nueva contraseña y su confirmación no coinciden'); window.location.href='perfil.php';</script>";
        }
    } else {
        echo "<script>alert('La contraseña actual es incorrecta'); window.location.href='perfil.php';</script>";
    }
}
?>