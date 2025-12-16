<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Admin | Fuego Dragón</title>
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    
    <link rel="stylesheet" href="activos/css/style.css"> 
    
    <style>
        /* Estilos generales para centrar el login */
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

        /* Contenedor del formulario (simulando la imagen de ejemplo) */
        .login-container {
            width: 350px;
            padding: 30px;
            background-color: #333d4b; /* Fondo oscuro del contenedor */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h1 {
            color: #ccc;
            font-size: 1.5em;
            margin-bottom: 30px;
            border-bottom: 2px solid #222427; /* Línea roja de separación */
            padding-bottom: 10px;
        }

        /* Campos de entrada */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #444;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #2b394d; /* Fondo de los inputs */
            color: #ccc;
            font-size: 1em;
        }
        
        /* Botón Entrar */
        .btn-entrar {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #008744; /* Verde más oscuro de la imagen */
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-entrar:hover {
            background-color: #007038;
        }

        /* Enlace de regreso */
        .back-link {
            display: block;
            margin-top: 20px;
            color: #CCC;
            text-decoration: none;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>ACCESO DE<br>ADMINISTRADOR</h1>
        
        <form action="adminPanel.php" method="POST">
            <input type="email" name="email" placeholder="Email" required value="holgereduardo777@gmail.com">
            <input type="password" name="password" placeholder="Contraseña" required>
            
            <button type="submit" class="btn-entrar">Entrar</button>
        </form>

        <a href="../LandingPage.php" class="back-link">← Ir a Landing Page</a>
    </div>

</body>
</html>