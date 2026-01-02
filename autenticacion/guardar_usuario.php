<?php
include("../conexion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre_completo']);
    $email    = strtolower(trim($_POST['email'])); // Normalización a minúsculas
    $password = $_POST['contrasena'];
    $pais     = trim($_POST['pais_residencia']);
    $ciudad   = trim($_POST['ciudad_residencia']);
    $prefijo  = $_POST['codigo_celular']; 
    $numero   = trim($_POST['celular']);
    $celular_completo = $prefijo . " " . $numero;

    // --- VALIDACIONES DE SEGURIDAD ROBUSTA ---

    // 1. Validar sintaxis de correo (Estándar RFC 5322)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?status=error&msg=El+formato+del+correo+electrónico+no+es+válido");
        exit();
    }

    // 2. Validar existencia real del dominio (MX Record)
    // Esto asegura que sea un proveedor real (Gmail, Outlook, Yahoo, etc.)
    $dominio = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($dominio, "MX")) {
        header("Location: registro.php?status=error&msg=El+dominio+del+correo+no+existe+o+no+es+válido");
        exit();
    }

    // 3. Validar complejidad de contraseña (Min 8 chars, 1 Mayúscula, 1 Número)
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        header("Location: registro.php?status=error&msg=La+contraseña+debe+tener+al+menos+8+caracteres,+una+mayúscula+y+un+número");
        exit();
    }

    // 4. Validar que el celular no esté registrado previamente
    $sql_check_cel = "SELECT id FROM usuarios WHERE celular = ?";
    $stmt_check = $conexion->prepare($sql_check_cel);
    $stmt_check->bind_param("s", $celular_completo);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        header("Location: registro.php?status=error&msg=El+número+de+celular+ya+se+encuentra+registrado.+Por+favor+utiliza+otro+numero.");
        exit();
    }
    $stmt_check->close();

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