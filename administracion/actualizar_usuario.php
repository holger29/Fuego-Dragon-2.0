<?php
session_start();
include("../conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['admin_id'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $pais = $_POST['pais_residencia'];
    $ciudad = $_POST['ciudad_residencia'];
    $celular = $_POST['celular'];

    $sql = "UPDATE usuarios SET nombre_completo=?, email=?, pais_residencia=?, ciudad_residencia=?, celular=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $email, $pais, $ciudad, $celular, $id);

    if ($stmt->execute()) {
        header("Location: adminPanel.php?status=success&msg=Usuario+actualizado");
    } else {
        header("Location: adminPanel.php?status=error&msg=Error+al+actualizar");
    }
    $stmt->close();
}
$conexion->close();
?>