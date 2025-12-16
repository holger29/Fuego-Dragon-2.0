<?php
    $ruta_login = "login.php";
    $ruta_regresar_home = "../LandingPage.php";
    //$ruta_dashboard = "../paginas/dashboard.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    
    <style>
        /* Estilos generales para centrar el formulario (reutilizando estilos de admin.php) */
        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow-y: auto;
        }

        /* Contenedor del formulario */
        .auth-container {
            max-width: 400px; /* Ancho máximo para el formulario */
            width: 90%; /* Ancho flexible para pantallas pequeñas */
            padding: 30px;
            background-color: #1a1a1a; 
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            box-sizing: border-box; /* Para que el padding no afecte el ancho total */
            margin: 20px 0; /* Margen vertical para evitar que se pegue en pantallas pequeñas */
        }

        h1 {
            color: #a30000;
            font-size: 2em;
            margin-bottom: 25px;
            border-bottom: 2px solid #333; 
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
            font-size: 0.9em;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2b2b2b;
            color: white;
            box-sizing: border-box;
        }
        
        /* Botón de Registrar (Rojo Fuego Dragón) */
        .btn-register {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            background-color: #a30000; 
            color: white;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .btn-register:hover {
            background-color: #880000;
        }

        /* Enlaces */
        .link-group {
            margin-top: 20px;
            font-size: 0.9em;
        }
        .link-group a {
            color: gray;
            text-decoration: none;
            transition: color 0.3s;
        }
        .link-group a:hover {
            color: #a30000;
        }
        .link-group .iniciar_sesion {
            font-weight: bold;
            color: rgb(2, 139, 64);
        }
     
        /* Mensajes de feedback (ocultos por defecto) */
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .message.error {
            background-color: #dc3545;
            color: white;
        }
        .message.success {
            background-color: #28a745;
            color: white;
        }

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 480px) {
            .auth-container {
                padding: 25px 20px;
            }
            h1 {
                font-size: 1.5em; /* Reducir un poco el título */
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>REGISTRO DE NUEVO USUARIO</h1>

        <form action="<?php echo $ruta_login; ?>" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="pais">País:</label>
                <input type="text" id="pais" name="pais">
            </div>
            
            <div class="form-group">
                <label for="celular">Celular:</label>
                <input type="text" id="celular" name="celular">
            </div>

            <button type="submit" class="btn-register">
                Registrar Usuario
            </button>
        </form>
        
        <div class="link-group">
            <a href="<?php echo $ruta_login; ?>">¿Ya tienes cuenta? <span class="iniciar_sesion">Inicia sesión aquí.</span></a>
            <br>
            <a href="<?php echo $ruta_regresar_home; ?>">← Volver a la página principal</a>
        </div>
    </div>
</body>
</html>