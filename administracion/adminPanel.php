<?php
// Versión sin conexión a base de datos ni validación de sesión
// Este código solo incluye la simulación de rutas.

$ruta_salir = "admin.php"; // Simula cierre de sesión o vuelta a una página inicial
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="activos/img/favicon_fd.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="activos/css/style.css"> 
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    
    <style>
        /* Estilos generales */
        html, body {
            height: 100%; 
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            overflow-y: scroll; 
        }

        /* HEADER del Admin */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: #1a1a1a;
            border-bottom: 2px solid #a30000;
        }
        .admin-header h1 {
            margin: 0;
            color: #f4f4f4;
            font-size: 2em;
            font-weight: bold;
        }
        .btn-salir {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background-color: #a30000;
            color: white;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        /* Contenido Principal y Estilo de Acordeón */
        .admin-content {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        /* Contenedores de Acordeón (Panel principal) */
        .admin-accordion-block {
            background-color: #1f2a38; 
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .accordion-header {
            padding: 20px 25px;
            background-color: #2b394d; 
            border-bottom: 1px solid #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }
        .accordion-header:hover {
            background-color: #334255;
        }
        .accordion-header h2 {
            color: #f4f4f4;
            font-size: 1.4em;
            margin: 0;
            font-weight: normal;
        }
        .accordion-arrow {
            font-size: 1.5em;
            transition: transform 0.3s;
            color: #8fa0b5;
        }
        .admin-accordion-block.active .accordion-arrow {
            transform: rotate(180deg);
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
            padding: 0 25px;
            color: #ccc;
        }
        .admin-accordion-block.active .accordion-content {
            max-height: 5000px; /* Suficiente para todo el contenido */
            padding-top: 25px; 
            padding-bottom: 25px;
        }

        /* Estilos para el acordeón de temporada (anidado) */
        .season-accordion-block.active .season-accordion-arrow {
            transform: rotate(180deg);
        }
        .season-accordion-content {
            display: none; /* Oculto por defecto, se muestra con JS */
        }
        
        /* ESTRUCTURA ESPECÍFICA DE GESTIONAR CONTENIDO */
        .content-management-area {
            background-color: #121417;
            padding: 20px;
            border-radius: 6px;
        }
        .upload-info {
            font-style: italic;
            color: #a30000;
            margin-bottom: 25px;
            border-bottom: 1px dashed #333;
            padding-bottom: 15px;
        }
        
        /* Listado de Temporadas */
        .saga-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .series-block {
            background-color: #1f2a38;
            border: 1px solid #3a4b63;
            border-radius: 6px;
            padding: 20px;
        }
        .series-title {
            font-size: 1.5em;
            color: #a30000;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #3a4b63;
        }
        .season-accordion-block {
            margin-bottom: 15px;
        }

        .episode-list {
            padding: 0;
            list-style: none;
            margin: 0;
            background-color: #2b394d;
        }
        .episode-item {
            display: grid;
            grid-template-columns: 5fr 1fr 0.5fr 0.5fr; /* Nombre, Estado, Ver, Acciones */
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #333;
            gap: 15px;
        }
        .episode-item:last-child {
            border-bottom: none;
        }
        .episode-name {
            color: #ccc;
        }
        .episode-status {
            text-align: center;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status-disponible {
            color: #28a745; /* Verde */
        }
        .status-pendiente {
            color: #ffc107; /* Amarillo */
        }
        .episode-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        /* Botones de Acción (Subir, Borrar, Previsualizar) */
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            height: 30px;
        }
        .action-btn.preview {
            background-color: #007bff; /* Azul para el ojo */
            color: white;
        }
        .action-btn.upload {
            background-color: #5a7493; /* Gris/Azul para subir */
            color: white;
        }
        .action-btn.delete {
            background-color: #dc3545; /* Rojo para borrar */
            color: white;
        }
        .action-btn:hover {
            opacity: 0.8;
        }

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 768px) {
            .admin-header {
                padding: 15px 20px;
            }
            .admin-header h1 {
                font-size: 1.5em;
            }
            .admin-content {
                margin: 20px auto;
            }
            .episode-item {
                grid-template-columns: 1fr; /* Apila los elementos */
                text-align: center;
            }
            .episode-name {
                margin-bottom: 10px;
            }
            .episode-status {
                margin-bottom: 10px;
            }
            .episode-actions {
                justify-content: center;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script para manejar TODOS los acordeones
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.admin-accordion-block');
                    block.classList.toggle('active');
                });
            });
        });
    </script>
</head>
<body>
    <header class="admin-header">
        <h1>PANEL DE ADMINISTRADOR</h1>
        <a href="<?php echo $ruta_salir; ?>" class="btn-salir">Salir</a>
    </header>

    <div class="admin-content">

        <div class="admin-accordion-block active">
            <div class="accordion-header">
                <h2>GESTIONAR CONTENIDO DE VIDEO</h2>
                <span class="accordion-arrow">V</span>
            </div>
            <div class="accordion-content">
                <div class="content-management-area">
                    <p class="upload-info">
                        Si ves un video como "Disponible" pero la carpeta de Storage está vacía, es un registro antiguo. Puedes usar el botón de **subir** (gris/azul) para reemplazarlo.
                    </p>
                    
                    <div class="saga-list">
                        
                        <div class="series-block">
                            <h3 class="series-title">GAME OF THRONES</h3>
                            
                            <?php 
                                // Función auxiliar para generar las listas de episodios (solo frontend)
                                function generate_episodes($season, $count, $available_until = 0) {
                                    $html = '';
                                    for ($i = 1; $i <= $count; $i++) {
                                        $is_available = ($i <= $available_until);
                                        $status_class = $is_available ? 'disponible' : 'pendiente';
                                        $status_text = $is_available ? 'Disponible' : 'Pendiente';

                                        $html .= '<div class="episode-item">';
                                        $html .= '<span class="episode-name">Episodio ' . $i . ': Capítulo ' . $i . ' (.mp4)</span>';
                                        $html .= '<span class="episode-status status-' . $status_class . '">' . $status_text . '</span>';
                                        
                                        // Botones de Acción
                                        $html .= '<span class="episode-actions">';
                                        if ($is_available) {
                                            $html .= '<button class="action-btn preview" title="Previsualizar"><i class="fa-solid fa-eye"></i></button>';
                                        }
                                        $html .= '<button class="action-btn upload" title="Subir/Reemplazar"><i class="fa-solid fa-upload"></i></button>';
                                        $html .= '<button class="action-btn delete" title="Borrar"><i class="fa-solid fa-trash"></i></button>';
                                        $html .= '</span>';
                                        $html .= '</div>';
                                    }
                                    return $html;
                                }

                                // Definición de Temporadas y Episodios
                                $go_t_seasons = [
                                    1 => 10, // T1: 10 episodios
                                    2 => 10,
                                    3 => 10,
                                    4 => 10,
                                    5 => 10,
                                    6 => 10,
                                    7 => 7,  // T7: 7 episodios
                                    8 => 6   // T8: 6 episodios
                                ];

                                // Generar las 8 temporadas
                                foreach ($go_t_seasons as $season => $episodes_count) {
                                    // Simulamos que T1 está disponible y las demás no
                                    $available_count = ($season == 1) ? $episodes_count : 0; 

                                    // Usamos la misma estructura de acordeón principal para las temporadas
                                    echo '<div class="admin-accordion-block">'; // Bloque de acordeón para la temporada
                                    echo '<div class="accordion-header">';
                                    echo '<h2>Temporada ' . $season . '</h2>';
                                    echo '<span class="accordion-arrow">V</span>';
                                    echo '</div>';
                                    echo '<div class="accordion-content episode-list">'; // El contenido es la lista de episodios
                                    echo generate_episodes($season, $episodes_count, $available_count);
                                    echo '</div>';
                                    echo '</div>';
                                }
                            ?>
                        </div>
                        
                        <div class="series-block">
                            <h3 class="series-title">HOUSE OF THE DRAGON</h3>
                            <?php 
                                // HotD (Simulamos 1 temporada con 10 episodios, 3 disponibles)
                                $hotd_episodes = 10;
                                $hotd_available = 3; 

                                echo '<div class="admin-accordion-block">';
                                echo '<div class="accordion-header"><h2>Temporada 1</h2><span class="accordion-arrow">V</span></div>';
                                echo '<div class="accordion-content episode-list">';
                                echo generate_episodes(1, $hotd_episodes, $hotd_available);
                                echo '</div>';
                                echo '</div>';
                            ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-accordion-block">
            <div class="accordion-header">
                <h2>GESTIONAR USUARIOS (5)</h2>
                <span class="accordion-arrow">V</span>
            </div>
            <div class="accordion-content">
                <p>Aquí irá la tabla de usuarios con opciones para editar y eliminar (CRUD de Usuarios).</p>
            </div>
        </div>

        <div class="admin-accordion-block">
            <div class="accordion-header">
                <h2>COMENTARIOS DE USUARIOS</h2>
                <span class="accordion-arrow">V</span>
            </div>
            <div class="accordion-content">
                <p>Aquí se mostrarán los comentarios y sugerencias enviados desde los feedback de los usuarios.</p>
            </div>
        </div>
    </div>
</body>
</html>