<?php
// Simulación de sesión (solo frontend)
$usuario = "holgereduardo777";
$ruta_perfil="perfil.php";
$ruta_LandingPage="../LandingPage.php";
$ruta_GoT="page_GoT.php";
$ruta_HotD="page_HotD.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fuego Dragón</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../activos/css/style.css">
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cinzel', serif;
            background: radial-gradient(circle at top, #0d1b2a, #000814);
            color: #fff;
            min-height: 100vh;
            overflow-y:auto;
        }

        /* HEADER */
        header {
            background: linear-gradient(to bottom, #000, #050505);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #222;
        }

        header h1 {
            font-size: 22px;
            letter-spacing: 2px;
        }

        .user-actions {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-actions span {
            opacity: 0.85;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .btn-logout {
            background: #d62828;
        }

        /* MAIN */
        main {
            padding: 20px 20px 40px 20px;
            text-align: center;
        }

        main h2 {
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .sagas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            max-width: 900px;
            margin: auto;
        }

        .card {
            position: relative;
            background: #000;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.7);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
        }

        .card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);
        }

        .card-content {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            z-index: 2;
            text-align: left;
        }

        .card-content h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card-content a {
            display: inline-block;
            background: #d62828;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 13px;
            text-decoration: none;
            color: #fff;
            transition: background 0.3s;
        }

        .card-content a:hover {
            background: #a4161a;
        }

        footer {
            text-align:center;
            margin-top: 60px;
            padding: 20px;
            font-size: 12px;
            opacity: 0.6;
        }
    </style>
</head>
<body>

<header>
    <h1>FUEGO DRAGÓN</h1>
    <div class="user-actions">
        <span>Bienvenido, <?= $usuario ?></span>
        <a href="<?php echo $ruta_perfil; ?>" class="btn btn-profile">Mi Perfil</a>
        <a href="<?php echo $ruta_LandingPage; ?>" class="btn btn-logout">Salir</a>
    </div>
</header>

<main>
    <h2>ELIGE TU SAGA</h2>

    <section class="sagas">

        <!-- Game of Thrones -->
        <article class="card">
            <img src="../activos/img/Game_of_Thrones.webp" alt="Game of Thrones">
            <div class="card-content">
                <h3>Game of Thrones</h3>
                <a href="<?php echo $ruta_GoT?>">Ver serie GoT</a>
            </div>
        </article>

        <!-- House of the Dragon -->
        <article class="card">
            <img src="../activos/img/lacasadeldragon.png" alt="House of the Dragon">
            <div class="card-content">
                <h3>House of the Dragon</h3>
                <a href="<?php echo $ruta_HotD?>">Ver serie HOTD</a>
            </div>
        </article>

    </section>
</main>

<footer>
    <p>
        © <?= date("Y") ?> Fuego Dragón - Todos los derechos reservados</br>
        V. 2.0.0
    </p>
        
</footer>

</body>
</html>