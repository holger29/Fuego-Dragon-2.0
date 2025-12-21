<?php
session_start();
include("../conexion/conexion.php"); // Asegúrate de que la ruta a tu conexión sea correcta

// 1. Validar que el administrador tenga sesión activa
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

// 2. Validar que se haya recibido un ID válido por la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // 3. Preparar la consulta para evitar inyecciones SQL
    $sql_delete = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql_delete);
    $stmt->bind_param("i", $id_usuario);

    if ($stmt->execute()) {
        // Redirigir de vuelta con un mensaje de éxito
        header("Location: adminPanel.php?status=success&msg=Usuario+eliminado");
    } else {
        // Redirigir con mensaje de error
        header("Location: adminPanel.php?status=error&msg=No+se+pudo+eliminar");
    }
    $stmt->close();
} else {
    // Si no hay ID, volvemos al panel
    header("Location: adminPanel.php");
}

$conexion->close();
exit();
?>