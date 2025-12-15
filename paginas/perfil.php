<?php
// SIMULACIÓN de variables de sesión para el frontend
$usuario_nombre = "Daniela María Hurtado";
$usuario_email = "danielam12@gmail.com";
$usuario_pais = "Nicaragua";
$usuario_ciudad = "Managua";
$usuario_celular = "50535985477";
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
    
    <style>
        /* Estilos generales */
        html, body {
            height: 100%; /* Aseguramos que el HTML y el Body ocupen 100% */
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #121212;
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            overflow-y: scroll; /* Forzamos la barra de desplazamiento */
        }
        .main-header {
         /* Aseguramos que la barra ocupe 100% de ancho */
            width: 100%; 
            box-sizing: border-box; /* Incluye padding en el ancho total */
            background-color: #1a1a1a;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #a30000;
        }
        .logo {
            font-size: 1.8em;
            color: #a30000;
            font-weight: bold;
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
        .btn-profile, .btn-logout {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: opacity 0.3s;
        }
        .btn-profile {
            background-color: #3f51b5; /* Azul */
            color: white;
        }
        .btn-logout {
            background-color: #a30000; /* Rojo */
            color: white;
        }
        .btn-profile:hover, .btn-logout:hover {
            opacity: 0.8;
        }
        .btn-back-to-dashboard {
            display: inline-block;
            background-color: #545454;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-back-to-dashboard:hover {
            background-color: rgb(45, 45, 45);
            border: solid 1px red;
            transform: translateY(-1px);
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
        /* ... (Estilos de h1 y btn-back-to-dashboard) ... */
        
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
        /* ... (Estilos de btn-edit) ... */

        /* Datos Personales y Contraseña (Contenedor principal en dos columnas) */
        .user-data-container {
            display: grid;
            grid-template-columns: 1fr 1fr; /* CREAMOS LAS DOS COLUMNAS */
            gap: 20px;
        }
        .data-display {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        /* ... (Estilos de data-item, data-label, data-value) ... */

        /* CONTENEDOR DE CAMBIO DE CONTRASEÑA (Acordeón que vive en la segunda columna) */
        .change-password-area {
            background-color: #2a2a2a;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #333;
        }
        .change-password-header {
            padding: 15px;
            background-color: #3a3a3a; /* Header distinto para el acordeón interno */
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
            width: 100%; /* Ocupa todo el ancho de la columna */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #121212;
            color: white;
            box-sizing: border-box;
        }
        .btn-update-password {
            background-color: #a30000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        /* ACORDEÓN GENERAL (Para Historial, Adquirido, Comentarios) */
        .accordion-block {
            /* ... (estilos iguales a los anteriores para el acordeón principal) ... */
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
        
        /* ... (Estilos de feedback y no-data) ... */

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
        <div class="logo">FUEGO DRAGÓN</div>
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
                        <span class="data-value"><?php echo htmlspecialchars($usuario_celular); ?></span>
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
</body>
</html>