<?php
session_start();
include("../conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['admin_id'])) {
    
    $id = $_POST['id'];
    $pass = $_POST['nueva_password'];

    if (!empty($pass)) {
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