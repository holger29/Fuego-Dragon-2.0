<?php
/*cuando el usuario se logee aqui se verificarán las credenciales del  usuario
antes de entrar al dashboard.*/
// 1. Iniciar sesión para guardar los datos del usuario si el login es exitoso
session_start();

// 2. Incluir la conexión
include("../conexion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['contrasena'];

    // 3. Buscar al usuario por email usando sentencias preparadas
    $sql = "SELECT id, nombre_completo, contrasena FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // 4. Verificar si el email existe
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // 5. Comparar la contraseña escrita con el hash de la BD
        if (password_verify($password, $usuario['contrasena'])) {
            
            // ¡ÉXITO! Guardamos datos en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];

            // Redirigir al Dashboard
            header("Location: ../paginas/dashboard.php");
            exit();
        } else {
            // Redirigir con error de contraseña
            header("Location: login.php?error=wrong_password&email=" . urlencode($email));
            exit();
        }
    } else {
        // Redirigir con error de usuario no encontrado
        header("Location: login.php?error=user_not_found");
        exit();
    }

    $stmt->close();
    $conexion->close();
}
?>