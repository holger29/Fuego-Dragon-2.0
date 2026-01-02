<?php
session_start();
include("../conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['admin_id'])) {
    
    $id = $_POST['id'];
    $pass = $_POST['nueva_password'];

    if (!empty($pass)) {
        // Validar complejidad de contraseña (Min 8 chars, 1 Mayúscula, 1 Número)
        if (strlen($pass) < 8 || !preg_match('/[A-Z]/', $pass) || !preg_match('/[0-9]/', $pass)) {
            header("Location: adminPanel.php?status=error&msg=La+contraseña+debe+tener+al+menos+8+caracteres,+una+mayúscula+y+un+número");
            exit();
        }

        // Encriptar la contraseña de forma segura
        $pass_encriptada = password_hash($pass, PASSWORD_BCRYPT);

        $sql = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $pass_encriptada, $id);

        if ($stmt->execute()) {
            header("Location: adminPanel.php?status=success&msg=Contraseña+actualizada+correctamente");
        } else {
            header("Location: adminPanel.php?status=error&msg=Error+al+actualizar+la+base+de+datos");
        }
        $stmt->close();
    } else {
        header("Location: adminPanel.php?status=error&msg=La+contraseña+no+puede+estar+vacía");
        header("Location: adminpanel.php?status=error&msg=La+contraseña+no+puede+estar+vacía");
    }
} else {
    header("Location: adminPanel.php");
    header("Location: adminpanel.php");
}

$conexion->close();
exit();
?>