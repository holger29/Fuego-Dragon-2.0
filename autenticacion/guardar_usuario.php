<?php
// 1. Incluimos el archivo de conexión. Ajusta la ruta si es necesario.
// Suponiendo que este archivo está en 'autenticacion/' y la conexión en 'conexion/conexion.php'
include("../conexion/conexion.php");

// 2. Verificamos si los datos fueron enviados por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Recogemos y limpiamos los datos básicos para evitar espacios en blanco
    $nombre   = trim($_POST['nombre_completo']);
    $email    = trim($_POST['email']);
    $password = $_POST['contrasena']; // La contraseña se procesará con hash después
    $pais     = trim($_POST['pais_residencia']);
    $ciudad   = trim($_POST['ciudad_residencia']);
    
    // 4. Combinamos el prefijo y el número de celular para guardarlo como un solo dato
    $prefijo  = $_POST['codigo_celular']; 
    $numero   = trim($_POST['celular']);
    $celular_completo = $prefijo . " " . $numero;

    // 5. Ciframos la contraseña. 
    // PASSWORD_DEFAULT utiliza el algoritmo más seguro actualmente (Bcrypt en 2025).
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 6. Preparamos la consulta SQL con marcadores de posición (?) por seguridad
    // Nota: Asegúrate de que los nombres de las columnas coincidan con tu tabla 'usuarios'
    $sql = "INSERT INTO usuarios (nombre_completo, email, contrasena, pais_residencia, ciudad_residencia, celular) VALUES (?, ?, ?, ?, ?, ?)";

    // 7. Preparamos la sentencia en la conexión
    $stmt = $conexion->prepare($sql);

    if ($stmt) {
        // 8. Vinculamos los parámetros. La "ssssss" significa que los 6 datos son de tipo String (cadena)
        $stmt->bind_param("ssssss", $nombre, $email, $password_hash, $pais, $ciudad, $celular_completo);

        // 9. Ejecutamos la consulta
        if ($stmt->execute()) {
            // Si tiene éxito, redirigimos al login con un mensaje de éxito
            echo "<script>
                    alert('¡Usuario registrado con éxito!');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            // Si hay un error (por ejemplo, email duplicado si la columna es UNIQUE)
            echo "Error al registrar: " . $stmt->error;
        }

        // 10. Cerramos la sentencia para liberar recursos
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conexion->error;
    }

    // 11. Cerramos la conexión a la base de datos
    $conexion->close();

} else {
    // Si alguien intenta entrar a este archivo directamente sin el formulario, lo mandamos al registro
    header("Location: registro.php");
    exit();
}
?>