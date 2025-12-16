<?php
// SIMULACIÓN de variables de sesión para el frontend
$usuario_nombre = "Daniela María Hurtado";
$usuario_email = "danielam12@gmail.com";
$usuario_pais = "Nicaragua";
$usuario_ciudad = "Managua";
// DATOS SIMULADOS: Se divide el número en prefijo y número
$usuario_celular_prefijo = "+505"; // Prefijo de Nicaragua
$usuario_celular_numero = "35985477"; // Número sin prefijo

$ruta_dashboard = "dashboard.php";
$ruta_salir = "../LandingPage.php"; // Simula cierre de sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos generales */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #121212;
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            overflow-y: scroll;
        }
        .main-header {
            width: 100%; 
            box-sizing: border-box;
            background-color: #1a1a1a;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #a30000;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        .logo-container img {
            height: 35px;
            width: auto;
        }
        .logo-container h1 {
            color: #ccc;
            margin: 0;
            font-size: 1.5em;
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
        }
        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-greeting {
            font-size: 1em;
            color: #ccc;
        }
        
        .btn-logout {
            background-color: #a30000;
            color: white;
        }
        
        .h1 {
            text-align:center;
        }
        /* CONTENIDO ESPECÍFICO DEL PERFIL */
        .profile-content {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            padding-bottom: 80px; 
        }
        
        /* Secciones del Perfil */
        .profile-section {
            background-color: #1a1a1a;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .section-header h2 {
            color: #a30000;
            font-size: 1.5em;
            margin: 0;
        }

        /* Datos Personales y Contraseña (Contenedor principal en dos columnas) */
        .user-data-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .data-display {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Contenedor de cambio de contraseña */
        .change-password-area {
            background-color: #2a2a2a;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #333;
        }
        .change-password-header {
            padding: 15px;
            background-color: #3a3a3a;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .change-password-header h3 {
            color: #f4f4f4;
            margin: 0;
            font-size: 1.2em;
        }
        .arrow {
            font-size: 1.5em;
            transition: transform 0.3s;
            color: #ccc;
        }
        .change-password-area.active .arrow {
            transform: rotate(90deg);
            color: #a30000;
        }
        
        /* Contenido del Acordeón de Contraseña */
        .change-password-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
            padding: 0 20px;
        }
        .change-password-area.active .change-password-content {
            max-height: 500px;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .change-password-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #121212;
            color: white;
            box-sizing: border-box;
        }
        .btn-update-password {
            background-color: #3f51b5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        /* ACORDEÓN GENERAL (Historial, Adquirido, Comentarios) */
        .accordion-block {
            background-color: #1a1a1a;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .accordion-header {
            padding: 20px 25px;
            background-color: #2a2a2a; 
            border-bottom: 1px solid #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }
        .accordion-block.active .accordion-content {
            max-height: 500px; 
            padding-top: 25px; 
            padding-bottom: 25px;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
            padding: 0 25px;
        }

        .accordion-content textarea {
            width: 90%;
            min-height: 120px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
                text-align: center;
            }

            .profile-content {
                margin-top: 20px;
                padding: 0 15px 40px 15px;
            }
            .profile-content h1 {
                font-size: 1.8em;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .user-data-container {
                grid-template-columns: 1fr;
            }

            .accordion-content textarea {
                width: 100%;
                box-sizing: border-box;
            }
        }

        /* --- FOOTER --- */
        footer {
            text-align: center;
            margin-top: 60px;
            padding: 20px;
            font-size: 12px;
            opacity: 0.6;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script para manejar el efecto acordeón en las secciones principales
            const mainHeaders = document.querySelectorAll('.accordion-header');
            
            mainHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.accordion-block');
                    block.classList.toggle('active');
                });
            });

            // Script para manejar el acordeón de CAMBIAR CONTRASEÑA (interno)
            const passwordHeader = document.querySelector('.change-password-header');
            if (passwordHeader) {
                passwordHeader.addEventListener('click', function() {
                    const block = this.closest('.change-password-area');
                    block.classList.toggle('active');
                });
            }
        });
    </script>
</head>
<body>

    <header class="main-header">
        <a href="<?php echo $ruta_dashboard; ?>" class="logo-container">
            <img src="../activos/img/logo-fuegodragon-ok.png" alt="Fuego Dragón Logo">
            <h1>FUEGO DRAGÓN</h1>
        </a>
        <div class="user-actions">
            <span class="user-greeting">Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?></span>
            <a href="#" class="btn-profile">Mi Perfil</a> 
            <a href="<?php echo $ruta_salir; ?>" class="btn-logout">Salir</a>
        </div>
    </header>

    <div class="profile-content">
        <a href="<?php echo $ruta_dashboard; ?>" class="btn-back-to-dashboard">
            ← Volver al inicio (Sagas)
        </a>

        <h1 class="h1">MI PERFIL</h1>

        <div class="profile-section">
            <div class="section-header">
                <h2>INFORMACIÓN DE LA CUENTA</h2>
                <button class="btn-edit">Editar Datos</button>
            </div>
            
            <div class="user-data-container">
                <div class="data-display">
                    <div class="data-item">
                        <span class="data-label">Nombre:</span>
                        <span class="data-value"><?php echo htmlspecialchars($usuario_nombre); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Email:</span>
                        <span class="data-value"><?php echo htmlspecialchars($usuario_email); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">País:</span>
                        <span class="data-value"><?php echo htmlspecialchars($usuario_pais); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Ciudad:</span>
                        <span class="data-value"><?php echo htmlspecialchars($usuario_ciudad); ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Celular:</span>
                        <span class="data-value">
                            <?php echo htmlspecialchars($usuario_celular_prefijo . ' ' . $usuario_celular_numero); ?>
                        </span>
                    </div>
                </div>

                <div class="change-password-area">
                    <div class="change-password-header">
                        <h3>Cambiar Contraseña</h3>
                        <span class="arrow">></span>
                    </div>
                    <div class="change-password-content">
                        <form action="#" method="post" class="change-password-form">
                            <input type="password" name="current_password" placeholder="Contraseña Actual">
                            <input type="password" name="new_password" placeholder="Nueva Contraseña">
                            <input type="password" name="confirm_new_password" placeholder="Confirmar Nueva Contraseña">
                            <button type="submit" class="btn-update-password">Actualizar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="accordion-block">
            <div class="accordion-header">
                <h2>HISTORIAL DE VISUALIZACIÓN</h2>
                <span class="arrow">></span>
            </div>
            <div class="accordion-content">
                <p class="no-data">Aún no has visto ningún episodio.</p>
            </div>
        </div>
        
        <div class="accordion-block">
            <div class="accordion-header">
                <h2>CONTENIDO ADQUIRIDO (Descargas)</h2>
                <span class="arrow">></span>
            </div>
            <div class="accordion-content">
                <p class="no-data">No has comprado contenido adicional.</p>
            </div>
        </div>
        
        <div class="accordion-block">
            <div class="accordion-header">
                <h2>COMENTARIOS Y SUGERENCIAS</h2>
                <span class="arrow">></span>
            </div>
            <div class="accordion-content">
                <form action="#" method="post" class="feedback-form">
                    <textarea rows="5" placeholder="Cuéntanos tu experiencia o qué podemos mejorar..."></textarea>
                    <button type="submit" class="btn-submit">Enviar Feedback</button>
                </form>
            </div>
        </div>

    </div>

    <footer>
        <p>
            © <?php echo date("Y"); ?> Fuego Dragón - Todos los derechos reservados.<br>
            V. 2.0.0
        </p>
    </footer>
</body>
</html>