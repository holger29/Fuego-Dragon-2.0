<?php
session_start();
include("../conexion/conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../autenticacion/login.php");
    exit();
}

$ruta_dashboard = "dashboard.php"; // Volver al dashboard (en la misma carpeta)
$ruta_perfil = "perfil.php";
$ruta_LandingPage = "../LandingPage.php"; 

// Datos simulados para el frontend
$nombre_usuario = "holgereduardo777";
$titulo_serie = "HOUSE OF THE DRAGON";
$sinopsis = "La historia de la Casa Targaryen ambientada 200 años antes de los eventos de Juego de Tronos.";
$poster_img = "../activos/img/hotd_poster.jpg"; // Asegúrate de que esta imagen exista

// --- ESTRUCTURA DE TEMPORADAS Y EPISODIOS ---
// House of the Dragon (HofD) - 1 Temporada de 10 Episodios
function generar_episodios($cantidad, $num_temp, $precio) {
    $episodios = [];
    for ($i = 1; $i <= $cantidad; $i++) {
        $episodios[] = [
            'titulo' => 'Capítulo ' . $i,
            'resumen' => 'Temporada ' . $num_temp,
            'precio' => $precio,
        ];
    }
    return $episodios;
}

$temporadas = [
    1 => [
        'titulo' => 'Temporada 1 (10 Episodios)',
        'episodios' => generar_episodios(10, 1, '0.20'), // 10 episodios con precio 0.20
    ],
    // Si necesitas simular la Temporada 2, descomenta y ajusta:
    /*
    2 => [
        'titulo' => 'Temporada 2 (8 Episodios)',
        'episodios' => generar_episodios(8, 2, '0.30'), 
    ],
    */
];

// --- CONSULTAR DISPONIBILIDAD EN BD ---
$videos_disponibles = [];
$sql_v = "SELECT temporada, episodio FROM videos WHERE serie = 'HotD'";
$res_v = $conexion->query($sql_v);
while($row = $res_v->fetch_assoc()) {
    $videos_disponibles[$row['temporada'] . '_' . $row['episodio']] = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_serie; ?> | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&display=swap" rel="stylesheet">

    
    <style>
        /* CSS re-utilizado de page_GoT.php para consistencia */
        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background-color: #1a1a1a;
            border-bottom: 3px solid #a30000;
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
        .nav-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .nav-actions a, .nav-actions span {
            color: #ccc;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-logout {
            background-color: #a30000 !important;
            color: white !important;
            font-weight: bold;
        }

        .series-page-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px 50px 20px; 
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            margin-bottom: 25px;
            color: #ccc;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: color 0.3s;
        }
        .btn-back:hover {
            color: #a30000;
        }
        .btn-back span {
            margin-right: 8px;
            font-size: 1.2em;
        }

        /* En House of the Dragon, la imagen de la serie puede ser más pequeña y el título más grande */
        .series-detail-layout {
            display: grid;
            grid-template-columns: 350px 1fr; 
            gap: 40px;
            margin-bottom: 50px; 
        }
        
        .series-poster {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        .series-poster img {
            width: 100%;
            height: auto;
            display: block;
        }

        .series-info-content {
            padding-top: 5px;
        }
        .series-info-content h2 {
            font-size: 3em;
            color: white;
            margin: 0 0 10px 0;
        }
        .series-info-content p {
            color: #8fa0b5;
            font-size: 1.1em;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        
        /* ACORDEÓN (Temporadas) */
        .accordion-title {
            color: #a30000;
            font-size: 1.5em;
            margin-top: 40px;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .accordion-block {
            background-color: #1a1a1a; 
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .accordion-header {
            padding: 15px 20px;
            background-color: #2b2b2b; 
            border-bottom: 1px solid #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 1.1em;
            color: #ccc;
        }
        .accordion-arrow {
            font-size: 1.5em;
            transition: transform 0.3s;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
        }
        .accordion-block.active .accordion-content {
            max-height: 1200px; 
        }
        
        /* Estilos de Episodio */
        .episode-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #333;
        }
        .episode-item:last-child {
            border-bottom: none;
        }
        .episode-details {
            text-align: left;
        }
        .episode-details h4 {
            margin: 0;
            font-size: 1em;
            color: white;
        }
        .episode-details p {
            margin: 5px 0 0 0;
            font-size: 0.85em;
            color: #8fa0b5;
        }
        
        /* Iconos y botones de acción (simulados) */
        .episode-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end; /* A la derecha */
        }
        .action-icon {
            font-size: 1.5em;
            color: #8fa0b5;
            cursor: pointer;
        }
        .action-icon:hover {
            color: white;
        }
        .price-tag {
            background-color: #ffc107; /* Amarillo para el precio */
            color: black;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 0.8em;
        }

        /* Estilos para etiquetas de episodio (GRATIS y BLOQUEADO) */
        .tag-free {
            background-color: #28a745; /* Verde */
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9em;
        }

        .locked-icon {
            color: #a30000; /* Rojo por defecto */
            cursor: pointer;
            font-size: 1.8em; /* Hacemos el candado un poco más grande */
        }

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 768px) {
            /* Navbar */
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }
            .navbar h1 {
                font-size: 1.3em;
            }

            /* Contenedor principal */
            .series-page-container {
                padding: 0 15px 40px 15px;
                margin-top: 20px;
            }

            /* Layout de una columna */
            .series-detail-layout {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            /* Centrar poster */
            .series-poster {
                max-width: 300px;
                margin: 0 auto;
            }

            /* Ajustes de texto */
            .series-info-content h2 {
                font-size: 2.2em;
                text-align: center;
            }

            /* Episodios */
            .episode-item {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .episode-actions {
                justify-content: center;
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
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.accordion-block');
                    block.classList.toggle('active');
                    
                    const arrow = this.querySelector('.accordion-arrow');
                    if (block.classList.contains('active')) {
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            });
            
            // Abrir la primera temporada por defecto
            const firstHeader = accordionHeaders[0];
            if (firstHeader) {
                firstHeader.click(); 
            }
        });
    </script>
</head>
<body>
    <header class="navbar">
        <a href="<?php echo $ruta_dashboard; ?>" class="logo-container">
            <img src="../activos/img/logo-fuegodragon-ok.png" alt="Fuego Dragón Logo">
            <h1>FUEGO DRAGÓN</h1>
        </a>
        <div class="nav-actions">
            <span class="user-greeting">Bienvenido, <?php echo $nombre_usuario; ?></span>
            <a href="<?php echo $ruta_perfil; ?>" class="btn-profile">Mi Perfil</a>
            <a href="<?php echo $ruta_LandingPage; ?>" class="btn-logout">Salir</a>
        </div>
    </header>

    <div class="series-page-container">
        
        <a href="<?php echo $ruta_dashboard; ?>" class="btn-back-to-dashboard">
            ← Volver al inicio (Sagas)
        </a>

        <div class="series-detail-layout">
            
            <div class="series-poster">
                <img src="../activos/img/lacasadeldragon.png" alt="<?php echo $titulo_serie; ?>">
            </div>

            <div class="series-info-content">
                <h2><?php echo $titulo_serie; ?></h2>
                <p><?php echo $sinopsis; ?></p>

                <div class="accordion-title">EPISODIOS</div>

                <?php foreach ($temporadas as $num => $temp): ?>
                    <div class="accordion-block">
                        <div class="accordion-header">
                            <?php echo $temp['titulo']; ?>
                            <span class="accordion-arrow">V</span>
                        </div>
                        <div class="accordion-content">
                            <?php if (!empty($temp['episodios'])): ?>
                                <?php foreach ($temp['episodios'] as $i => $episodio): ?>
                                    <div class="episode-item">
                                        <div class="episode-details">
                                            <h4><?php echo $episodio['titulo']; ?></h4>
                                            <p><?php echo $episodio['resumen']; ?></p>
                                        </div>
                                        <div class="episode-actions">
                                            <?php 
                                                $ep_num = $i + 1;
                                                $key = $num . '_' . $ep_num;
                                                $disponible = isset($videos_disponibles[$key]);
                                            ?>

                                            <?php if ($disponible): ?>
                                                <a href="ver_video.php?serie=HotD&t=<?php echo $num; ?>&e=<?php echo $ep_num; ?>" class="fa-solid fa-circle-play fa-2x action-icon" title="Ver Online" style="text-decoration:none;"></a>
                                                <a href="procesar_descarga.php?serie=HotD&t=<?php echo $num; ?>&e=<?php echo $ep_num; ?>" class="fa-solid fa-download fa-2x action-icon" title="Descargar" style="text-decoration:none;"></a>
                                            <?php else: ?>
                                                <span class="fa-solid fa-lock fa-2x locked-icon" title="Bloqueado"></span>
                                                <span style="color:#555; font-size:0.8em;">Próximamente</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 15px 20px; color: #8fa0b5;">
                                    <p>No hay detalles de episodios disponibles para esta temporada.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
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