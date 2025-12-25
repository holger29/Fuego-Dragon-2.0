<?php
include("../conexion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre_completo']);
    $email    = trim($_POST['email']);
    $password = $_POST['contrasena'];
    $pais     = trim($_POST['pais_residencia']);
    $ciudad   = trim($_POST['ciudad_residencia']);
    $prefijo  = $_POST['codigo_celular']; 
    $numero   = trim($_POST['celular']);
    $celular_completo = $prefijo . " " . $numero;

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre_completo, email, contrasena, pais_residencia, ciudad_residencia, celular) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $nombre, $email, $password_hash, $pais, $ciudad, $celular_completo);

        try {
            // Intentamos ejecutar la inserción
            $stmt->execute();
            
            // Si funciona, redirigimos al login con éxito
            header("Location: login.php?status=success&msg=Registro+exitoso.+Por+favor+inicia+sesión.");
            exit();

        } catch (mysqli_sql_exception $e) {
            // Si falla, verificamos si es por duplicado (Código de error 1062)
            if ($e->getCode() == 1062) {
                header("Location: registro.php?status=error&msg=El+usuario+ya+se+encuentra+registrado+con+ese+correo");
            } else {
                header("Location: registro.php?status=error&msg=Ocurrió+un+error+en+el+sistema: " . urlencode($e->getMessage()));
            }
            exit();
        }
        $stmt->close();
    } else {
        header("Location: registro.php?status=error&msg=Error+en+la+base+de+datos");
        exit();
    }
    $conexion->close();
} else {
    header("Location: registro.php");
    exit();
}
?>