<?php
// PHP estático para definir rutas necesarias para los enlaces de frontend.
$ruta_dashboard = "dashboard.php"; 
$ruta_perfil = "perfil.php";
$ruta_logout = "../autenticacion/logout.php"; 

$nombre_usuario = "holgereduardo777";
$titulo_serie = "GAME OF THRONES";
$sinopsis = "Nueve familias nobles luchan por el control de las tierras de Westeros, mientras un antiguo enemigo 
    regresa después de estar inactivo durante milenios.";

// --- ESTRUCTURA DE TEMPORADAS CORREGIDA ---
// Función para generar títulos y resúmenes de episodios
function generar_episodios($cantidad, $num_temp) {
    $episodios = [];
    for ($i = 1; $i <= $cantidad; $i++) {
        $episodios[] = [
            'titulo' => 'Capítulo ' . $i,
            'resumen' => 'Resumen del S' . $num_temp . 'E' . $i . '. Sinopsis detallada de la trama de este episodio.',
        ];
    }
    return $episodios;
}

$temporadas = [
    1 => [
        'titulo' => 'Temporada 1',
        'episodios' => generar_episodios(10, 1),
    ],
    2 => [
        'titulo' => 'Temporada 2 ',
        'episodios' => generar_episodios(10, 2),
    ],
    3 => [
        'titulo' => 'Temporada 3 ',
        'episodios' => generar_episodios(10, 3),
    ],
    4 => [
        'titulo' => 'Temporada 4 ',
        'episodios' => generar_episodios(10, 4),
    ],
    5 => [
        'titulo' => 'Temporada 5 ',
        'episodios' => generar_episodios(10, 5),
    ],
    6 => [
        'titulo' => 'Temporada 6 ',
        'episodios' => generar_episodios(10, 6),
    ],
    7 => [
        'titulo' => 'Temporada 7',
        'episodios' => generar_episodios(7, 7), // 7 episodios
    ],
    8 => [
        'titulo' => 'Temporada 8',
        'episodios' => generar_episodios(6, 8), // 6 episodios
    ],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_serie; ?> | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    
    <style>
        /* Estilos generales del cuerpo */
        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden; /* Evitar scroll horizontal */
            overflow-y: auto; /* Habilitar scroll vertical */
        }

        /* BARRA DE NAVEGACIÓN SUPERIOR (NAVBAR) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background-color: #1a1a1a;
            border-bottom: 3px solid #a30000;
        }
        .navbar h1 {
            color: #ccc;
            margin: 0;
            font-size: 1.5em;
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

        /* CONTENEDOR PRINCIPAL DE LA SERIE */
        .series-page-container {
            max-width: 1400px;
            margin: 30px auto;
            /* Aumentamos el padding inferior para dar espacio para el scroll */
            padding: 0 20px 50px 20px; 
        }
        
        /* Botón de Regreso */
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

        /* LAYOUT PRINCIPAL (Imagen + Información/Acordeón) */
        .series-detail-layout {
            display: grid;
            grid-template-columns: 350px 1fr; 
            gap: 40px;
            margin-bottom: 50px; 
        }
        
        /* IZQUIERDA: IMAGEN DE LA SERIE */
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

        /* DERECHA: INFORMACIÓN Y EPISODIOS */
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
        
        /* SECCIÓN DE ACORDEONES (Temporadas) */
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
        /* Muestra solo la primera temporada abierta por defecto para el ejemplo */
        .accordion-block:nth-child(2) .accordion-arrow {
            /* Simular abierto al inicio si es necesario, si no, usa el JS */
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
        }
        .accordion-block.active .accordion-content {
            /* Altura suficiente para mostrar todos los 10 episodios sin problemas de scroll interno */
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
            background-color: gold;
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
            transition: color 0.3s ease;
        }
        .locked-icon:hover {
            color: #28a745; /* Verde al pasar el mouse */
        }

    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script para manejar los acordeones (Temporadas)
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.accordion-block');
                    block.classList.toggle('active');
                    
                    // Gira la flecha
                    const arrow = this.querySelector('.accordion-arrow');
                    if (block.classList.contains('active')) {
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            });
            
            // Abrir la primera temporada por defecto para el demo
            const firstHeader = accordionHeaders[0];
            if (firstHeader) {
                firstHeader.click(); // Simula el clic para abrir
            }
        });
    </script>
</head>
<body>
    <header class="navbar">
        <h1>FUEGO DRAGÓN</h1>
        <div class="nav-actions">
            <span class="user-greeting">Bienvenido, <?php echo $nombre_usuario; ?></span>
            <a href="<?php echo $ruta_perfil; ?>" class="btn-profile">Mi Perfil</a>
            <a href="<?php echo $ruta_logout; ?>" class="btn-logout">Salir</a>
        </div>
    </header>

    <div class="series-page-container">
        
        <!-- Botón de volver reutilizable -->
        <a href="<?php echo $ruta_dashboard; ?>" class="btn-back-to-dashboard">← Volver a la selección</a>

        <div class="series-detail-layout">
            
            <div class="series-poster">
                <img src="../activos/img/Game_of_Thrones.webp" alt="<?php echo $titulo_serie; ?>">
            </div>

            <div class="series-info-content">
                <h2><?php echo $titulo_serie; ?></h2>
                <p><?php echo $sinopsis; ?></p>

                <div class="accordion-title">EPISODIOS POR TEMPORADA</div>

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
                                            <h4><?php echo 'E' . ($i + 1) . ': ' . $episodio['titulo']; ?></h4>
                                            <p><?php echo $episodio['resumen']; ?></p>
                                        </div>
                                        <div class="episode-actions">
                                            <?php if ($num == 1 && $i < 4): // Temporada 1, primeros 4 episodios (índices 0, 1, 2, 3) ?>
                                                <span class="tag-free">GRATIS</span>
                                                <span class="fa-solid fa-circle-play fa-2x action-icon" title="Ver"></span> 
                                                <span class="fa-solid fa-download fa-2x action-icon" title="descargar"></span> 
                                            <?php else: // Resto de episodios bloqueados ?>
                                                <span class="fa-solid fa-lock fa-2x locked-icon" title="Bloqueado"></span>
                                                <span class="fa-solid fa-circle-play fa-2x action-icon" title="Ver"></span>
                                                <span class="fa-solid fa-download fa-2x action-icon" title="Descargar"></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 15px 20px; color: #8fa0b5;">
                                    <p>Error de carga de episodios.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            </div>
        </div>
    </div>
</body>
</html>