<?php
    $ruta_dashboard = "../paginas/dashboard.php";
    
    // Capturar mensajes de error y email previo si existen
    $email_val = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
    $error_msg = '';
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'wrong_password') {
            $error_msg = 'Contraseña incorrecta.';
        } elseif ($_GET['error'] === 'user_not_found') {
            $error_msg = 'El correo no está registrado.';
        }
    }
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
            min-height: 100vh; /* Permite scroll si la pantalla es muy baja */
            margin: 0;
            padding: 15px; /* Espacio de seguridad en bordes */
            box-sizing: border-box;
        }

        /* Contenedor del formulario */
        .login-container {
            max-width: 350px;
            width: 90%;
            padding: 30px;
            background-color: #1a1a1a; /* Fondo oscuro */
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            box-sizing: border-box; /* CRUCIAL: Evita que el padding rompa el ancho en móviles */
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

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
            h1 {
                font-size: 1.5em;
            }
            .modal-content {
                width: 95%;
            }
        }

        /* Estilos del Modal de Recuperación */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.85); 
            backdrop-filter: blur(3px);
        }
        .modal-content {
            background-color: #1a1a1a;
            margin: 10% auto; 
            padding: 30px;
            border: 1px solid #a30000;
            width: 90%; 
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
            position: relative;
            box-shadow: 0 0 20px rgba(163, 0, 0, 0.3);
            box-sizing: border-box;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: white; }
        .modal h2 { color: #f4f4f4; font-size: 1.5em; margin-bottom: 15px; border-bottom: none; }
        .modal p { color: #ccc; margin-bottom: 20px; font-size: 0.9em; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>INICIO DE SESIÓN</h1>

        <form action="validar_login.php" method="POST">

            <input type="email" name="email" placeholder="Email" required value="<?php echo $email_val; ?>">
            <input type="password" name="contrasena" placeholder="Contraseña" required style="margin-bottom: 10px;">
            
            <?php if ($error_msg): ?>
                <div style="color: #dc3545; margin-bottom: 10px; font-size: 0.9em; text-align: left;"><?php echo $error_msg; ?></div>
            <?php endif; ?>
            
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="#" onclick="abrirModal()" style="color: #8fa0b5; font-size: 0.85em; text-decoration: none;">Olvidé mi contraseña</a>
            </div>

            <button type="submit" class="btn-acceder">
               Acceder
            </button>
        </form>
        
        <div class="link-group">
            <a href="registro.php">¿No tienes cuenta? Regístrate aquí.</a>
            <br>
            <a href="../index.php">← Volver a la página principal</a>
        </div>
    </div>

    <!-- Modal de Recuperación de Contraseña -->
    <div id="modalRecuperar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Recuperar Contraseña</h2>
            <p>Ingresa tu correo electrónico registrado y te enviaremos las instrucciones para restablecer tu contraseña.</p>
            <form onsubmit="enviarSolicitudPassword(event)">
                <input type="email" id="email_recuperacion" name="email_recuperacion" placeholder="Tu correo electrónico" required>
                <button type="submit" class="btn-acceder">Enviar solicitud de restablecimiento de contraseña</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById('modalRecuperar').style.display = 'block';
        }
        function cerrarModal() {
            document.getElementById('modalRecuperar').style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target == document.getElementById('modalRecuperar')) {
                cerrarModal();
            }
        }

        function enviarSolicitudPassword(e) {
            e.preventDefault();
            var email = document.getElementById('email_recuperacion').value;
            var adminEmail = "holgereduardo777@outlook.com";
            var asunto = "Restablecimiento de contraseña";
            var cuerpo = "Cordial saludos equipo de Fuego dragón, el presente correo es para solicitar el restablecimiento de mi contraseña. Quedo atent@ muchas gracias. atentamente: '" + email + "'";
            
            var mailtoLink = "mailto:" + adminEmail + "?subject=" + encodeURIComponent(asunto) + "&body=" + encodeURIComponent(cuerpo);
            
            window.location.href = mailtoLink;
            
            alert("Una vez se abra tu correo predeterminado podras enviar tu solicitud. El equipo de Fuego dragón te responderá en menos de 24 horas");
            cerrarModal();
        }
    </script>
</body>
</html>