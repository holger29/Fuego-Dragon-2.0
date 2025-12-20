<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuego Dragón</title>
    <link rel="stylesheet" href="activos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    <!--Favicon-->
    <link rel="icon" type="image/png" href="activos/img/favicon_fd.png">
    <style>
        /* Estilos para el logo de fondo */
        header {
            position: relative; /* Necesario para posicionar el logo absolutamente dentro */
            text-align: center;
        }
        .logo-background {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 400px; /* Ajusta el tamaño del logo como fondo */
            width: 80%;
            height: auto;
            opacity: 0.3; /* Hacemos el logo semitransparente para que no oculte el texto */
            z-index: 1; /* Se asegura que esté detrás del texto */
        }
        /* Nos aseguramos que el texto esté por encima del logo */
        header h1, header p, header .btn-registro {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>

    <div class="content-wrapper">
        <header>
            <!-- Imagen de fondo -->
            <img src="activos/img/logo-fuegodragon-ok.png" alt="Logo Fuego Dragón de fondo" class="logo-background">
            
            <!-- Contenido del header -->
            <h1>FUEGO DRAGÓN</h1>
            <p>Tu portal exclusivo para las sagas de Westeros. Regístrate para acceder a un mundo de fantasía, intriga y batallas épicas.</p>
            <a href="autenticacion/registro.php" class="btn-registro">Ingresar / Registrar</a>
        </header>

        <footer class="admin-link">
            <a href="administracion/admin.php">Admin Panel</a>
        </footer>
    </div>

</body>
</html>