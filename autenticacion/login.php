<?php
    $ruta_dashboard = "../paginas/dashboard.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    
    <style>
        /* Estilos generales para centrar el formulario */
        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Contenedor del formulario */
        .login-container {
            width: 350px;
            padding: 30px;
            background-color: #1a1a1a; /* Fondo oscuro */
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h1 {
            color: #a30000; /* Rojo Fuego Dragón */
            font-size: 2em;
            margin-bottom: 25px;
            border-bottom: 2px solid #333; 
            padding-bottom: 10px;
        }

        /* Campos de entrada */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #333;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #2b2b2b;
            color: white;
            font-size: 1em;
        }
        
        /* Botón de Acceder (Rojo Fuego Dragón) */
        .btn-acceder {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            background-color: #a30000; 
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-acceder:hover {
            background-color: #880000;
        }

        /* Enlaces */
        .link-group {
            margin-top: 20px;
            font-size: 0.9em;
        }
        .link-group a {
            color: #8fa0b5; /* Gris/Azul claro para enlaces */
            text-decoration: none;
            transition: color 0.3s;
        }
        .link-group a:hover {
            color: #a30000;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>INICIO DE SESIÓN</h1>

        <form action="validar_login.php" method="POST">

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>

            <button type="submit" class="btn-acceder">
               Acceder
            </button>
        </form>
        
        <div class="link-group">
            <a href="registro.php">¿No tienes cuenta? Regístrate aquí.</a>
            <br>
            <a href="../LandingPage.php">← Volver a la página principal</a>
        </div>
    </div>
</body>
</html>