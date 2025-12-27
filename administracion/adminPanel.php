<?php
/*El siguiente campo es para el administrador*/
session_start();
include("../conexion/conexion.php"); //para incluir la conexión para el feedback

// Si no existe la sesión de administrador, lo expulsamos al login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}
// Consulta para obtener los comentarios uniendo la tabla feedback con usuarios
// 1. Procesar la marcación como leído si se recibe el ID por POST
if (isset($_POST['marcar_leido'])) {
    $id_feedback = $_POST['id_feedback'];
    // Actualizamos el campo 'leido' a 1 para ese ID específico
    $sql_update = "UPDATE feedback SET leido = 1 WHERE id = ?";
    $stmt_upd = $conexion->prepare($sql_update);
    $stmt_upd->bind_param("i", $id_feedback);
    $stmt_upd->execute();
}

// 2. Consulta mejorada: seleccionamos el ID y el estado 'leido'
$sql_feedback = "SELECT f.id, f.mensaje, f.fecha_envio, f.leido, u.nombre_completo, u.email 
                 FROM feedback f 
                 INNER JOIN usuarios u ON f.usuario_id = u.id 
                 ORDER BY f.leido ASC, f.fecha_envio DESC"; // Primero los NO leídos, luego por fecha
//Ejecutamos la consulta con la variable $conexion (ya definida por el include)
$res_feedback = $conexion->query($sql_feedback);

// Versión sin conexión a base de datos ni validación de sesión
// Este código solo incluye la simulación de rutas.
$ruta_salir = "../autenticacion/logout.php"; // cierre de sesión o vuelta a una página inicial
?>

<?php
/**Esta parte le corresponde a adminPanel.php en la seccion
 * de gestionar usuarios */
// --- VARIABLES DE ESTADO Y BÚSQUEDA ---
$edit_id = $_GET['edit_id'] ?? null;
$reset_id = $_GET['reset_id'] ?? null;
$status = $_GET['status'] ?? null;
$search = $_GET['search'] ?? '';
$msg = $_GET['msg'] ?? '';

// Clases automáticas para que los acordeones se mantengan abiertos según la acción
$clase_usuarios = ($edit_id || $reset_id || $status || !empty($search)) ? 'active' : '';

// 3. Consulta Usuarios con BUSCADOR MULTICRITERIO
$sql_usuarios = "SELECT * FROM usuarios WHERE id LIKE '%$search%' OR nombre_completo LIKE '%$search%' OR email LIKE '%$search%' OR pais_residencia LIKE '%$search%' OR ciudad_residencia LIKE '%$search%' OR celular LIKE '%$search%' ORDER BY id DESC";
$res_usuarios = $conexion->query($sql_usuarios);

// 4. Obtener videos existentes para verificar disponibilidad
$video_map = [];
$sql_all_videos = "SELECT * FROM videos";
$res_all_videos = $conexion->query($sql_all_videos);
if ($res_all_videos) {
    while ($vid = $res_all_videos->fetch_assoc()) {
        // Clave única: Serie_Temporada_Episodio
        $key = $vid['serie'] . '_' . $vid['temporada'] . '_' . $vid['episodio'];
        $video_map[$key] = $vid;
    }
}

// 5. Consulta Historial de Compras
$sql_compras = "SELECT c.id, c.serie, c.temporada, c.fecha_compra, u.email, u.nombre_completo 
                FROM compras c 
                JOIN usuarios u ON c.usuario_id = u.id 
                ORDER BY c.fecha_compra DESC";
$res_compras = $conexion->query($sql_compras);

// --- CONTADORES PARA NOTIFICACIONES ---
// 1. Contar Feedback sin leer
$sql_count_f = "SELECT COUNT(*) as total FROM feedback WHERE leido = 0";
$res_count_f = $conexion->query($sql_count_f);
$total_feedback = ($res_count_f) ? $res_count_f->fetch_assoc()['total'] : 0;

// 2. Contar Total de Compras
$sql_count_c = "SELECT COUNT(*) as total FROM compras";
$res_count_c = $conexion->query($sql_count_c);
$total_compras = ($res_count_c) ? $res_count_c->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador | Fuego Dragón</title>

    <link rel="icon" type="image/png" href="activos/img/favicon_fd.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/style.css">
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">

    <style>
        /* Estilos generales */
        html,
        body {
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
        
        .alert-msg {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .alert-success { background-color: #28a745; color: white; }
        .alert-error { background-color: #dc3545; color: white; }

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
            max-height: 10000px;
            /* Aumentado para asegurar que todo el contenido sea visible */
            padding-top: 20px;
            padding-bottom: 20px;
        }

        /* Estilos para el acordeón de temporada (anidado) */
        .season-accordion-block.active .season-accordion-arrow {
            transform: rotate(180deg);
        }

        .season-accordion-content {
            display: none;
            /* Oculto por defecto, se muestra con JS */
        }

        /* ESTRUCTURA ESPECÍFICA DE GESTIONAR CONTENIDO */
        .content-management-area {
            background-color: #121417;
            padding: 20px;
            border-radius: 6px;
            overflow-y: auto;
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
            grid-template-columns: 5fr 1fr 0.5fr 0.5fr;
            /* Nombre, Estado, Ver, Acciones */
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
            color: #28a745;
            /* Verde */
        }

        .status-pendiente {
            color: #ffc107;
            /* Amarillo */
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
            background-color: #007bff;
            /* Azul para el ojo */
            color: white;
        }

        .action-btn.upload {
            background-color: #5a7493;
            /* Gris/Azul para subir */
            color: white;
        }
        
        .action-btn.link {
            background-color: #6f42c1;
            /* Morado para vincular */
            color: white;
        }

        .action-btn.delete {
            background-color: #dc3545;
            /* Rojo para borrar */
            color: white;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        /* --- ESTILOS PARA COMENTARIOS DE USUARIOS --- */
        .feedback-item {
            background-color: #2b394d;
            border: 1px solid #3a4b63;
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 15px;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #3a4b63;
            font-size: 0.9em;
            flex-wrap: wrap;
            /* Para que se ajuste en pantallas pequeñas */
        }

        .feedback-user {
            font-weight: bold;
            color: #f4f4f4;
        }

        .feedback-date {
            color: #8fa0b5;
        }

        .feedback-body p {
            margin: 0;
            line-height: 1.6;
            color: #ccc;
        }

        /* Estilos para badges de notificación */
        .badge-notification {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            background-color: #007bff; /* Azul por defecto */
            color: white;
            font-size: 0.6em; /* Ajustar según el tamaño de tu H2 */
            padding: 5px 10px;
            border-radius: 20px;
            margin-left: 15px;
            vertical-align: middle;
            font-weight: normal;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        /* Estilo específico para alertas (Feedback sin leer) */
        .badge-alert {
            background-color: #d62828; /* Rojo */
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(214, 40, 40, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(214, 40, 40, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(214, 40, 40, 0); }
        }


        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 768px) {
            .content-management-area {
                padding: 5px;
            }

            .admin-header {
                padding: 10px;
                padding: 15px 10px;
                /* Reducido padding horizontal */
            }

            .admin-header h1 {
                font-size: 1.5em;
            }

            .accordion-header {
                padding: 3px;
                padding: 15px 10px;
                /* Reducido padding horizontal */
            }

            .admin-accordion-block.active .accordion-content {
                padding-top: 20px;
                padding-bottom: 20px;
                padding-left: 10px;
                padding-right: 10px;
                overflow-y: auto;
            }

            .admin-content {
                margin: 10px auto;
                padding: 0 2px;
                /* Reducido padding horizontal del contenedor principal */
            }

            .episode-item {
                grid-template-columns: 1fr;
                /* Apila los elementos */
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
        //adminPanel.php (gestionar usuarios)
        // Función para confirmar eliminación de usuario
        function confirmarEliminar(id, nombre) {
            if (confirm("¿Estás seguro de eliminar permanentemente al usuario: " + nombre + "?")) {
                // Redirige a un archivo que procese el borrado
                window.location.href = "eliminar_usuario.php?id=" + id;
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const accordionHeaders = document.querySelectorAll('.accordion-header');

            // 1. Al cargar la página, revisar si había un acordeón abierto
            const activeAccordionId = localStorage.getItem('activeAccordion');
            if (activeAccordionId) {
                const activeBlock = document.getElementById(activeAccordionId);
                if (activeBlock) {
                    activeBlock.classList.add('active');
                    // Opcional: Hacer scroll suave hasta el acordeón abierto
                    activeBlock.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }

            accordionHeaders.forEach((header, index) => {
                // Asignamos un ID único si no lo tienen para poder identificarlos
                const block = header.closest('.admin-accordion-block');
                if (!block.id) block.id = 'accordion-' + index;

                header.addEventListener('click', function() {
                    // Alternar la clase active
                    block.classList.toggle('active');

                    // 2. Guardar o borrar el estado en localStorage
                    if (block.classList.contains('active')) {
                        localStorage.setItem('activeAccordion', block.id);

                        // Opcional: Cerrar los demás si quieres que solo uno esté abierto
                        document.querySelectorAll('.admin-accordion-block').forEach(other => {
                            if (other !== block) {
                                other.classList.remove('active');
                            }
                        });
                    } else {
                        localStorage.removeItem('activeAccordion');
                    }
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

        <?php if(!empty($msg)): ?>
            <div class="alert-msg <?php echo ($status == 'error') ? 'alert-error' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div class="admin-accordion-block">
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
                            // Definimos las 8 temporadas de GoT (T1-T6: 10 caps, T7: 7 caps, T8: 6 caps)
                            $got_seasons = [
                                1 => 10, 2 => 10, 3 => 10, 4 => 10, 
                                5 => 10, 6 => 10, 7 => 7,  8 => 6
                            ];

                            foreach ($got_seasons as $s => $eps) {
                                echo '<div class="admin-accordion-block">';
                                echo '<div class="accordion-header"><h2>Temporada ' . $s . '</h2><span class="accordion-arrow">V</span></div>';
                                echo '<div class="accordion-content episode-list">';
                                echo generate_episodes('GoT', $s, $eps, $video_map);
                                echo '</div>';
                                echo '</div>';
                            }

                            // Función corregida para generar las listas de episodios con formularios de subida
                            function generate_episodes($serie, $season, $count, $map)
                            {
                                $html = '';

                                for ($i = 1; $i <= $count; $i++) {
                                    // Verificar si existe en el mapa de videos
                                    $key = $serie . '_' . $season . '_' . $i;
                                    $video_data = isset($map[$key]) ? $map[$key] : null;
                                    $is_available = ($video_data !== null);

                                    $status_class = $is_available ? 'disponible' : 'pendiente';
                                    $status_text = $is_available ? 'Disponible' : 'Pendiente';

                                    $html .= '<div class="episode-item">';
                                    $html .= '<span class="episode-name">Episodio ' . $i . ': Capítulo ' . $i . ' (.mp4)</span>';
                                    $html .= '<span class="episode-status status-' . $status_class . '">' . $status_text . '</span>';

                                    $html .= '<span class="episode-actions">';

                                    // --- FORMULARIO PARA SUBIR (Se mantiene igual) ---
                                    $html .= '<form action="procesar_video.php" method="POST" enctype="multipart/form-data" style="display:inline;">';
                                    $html .= '  <input type="hidden" name="serie" value="' . $serie . '">';
                                    $html .= '  <input type="hidden" name="temporada" value="' . $season . '">';
                                    $html .= '  <input type="hidden" name="episodio" value="' . $i . '">';
                                    $input_id = "file_" . $serie . "_" . $season . "_" . $i;
                                    $html .= '  <input type="file" name="video_file" id="' . $input_id . '" style="display:none;" onchange="uploadToFirebase(this)">';
                                    $html .= '  <button type="button" class="action-btn upload" title="Subir" onclick="document.getElementById(\'' . $input_id . '\').click()">';
                                    $html .= '      <i class="fa-solid fa-upload"></i>';
                                    $html .= '  </button>';
                                    $html .= '</form>';
                                    
                                    // --- BOTÓN VINCULAR (NUEVO) ---
                                    $html .= '<button type="button" class="action-btn link" title="Vincular URL existente (Firebase)" onclick="linkExistingVideo(\'' . $serie . '\', ' . $season . ', ' . $i . ')">';
                                    $html .= '  <i class="fa-solid fa-link"></i>';
                                    $html .= '</button>';

                                    // --- AQUÍ COLOCAS EL CÓDIGO QUE ME MOSTRASTE (Botón Preview) ---
                                    if ($is_available) {
                                        $ruta_db = $video_data['ruta_archivo'];
                                        
                                        // Verificar si es una URL de Firebase (comienza con http) o archivo local
                                        if (strpos($ruta_db, 'http') === 0) {
                                            $path = $ruta_db;
                                        } else {
                                            $folder = ($serie == 'GoT') ? 'got' : 'hotd';
                                            $path = "../activos/videos/" . $folder . "/" . $ruta_db;
                                        }

                                        // Sanitizar variables para evitar errores de sintaxis en JS o HTML
                                        $safe_path = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
                                        $safe_title = htmlspecialchars($serie . ' - T' . $season . ' E' . $i, ENT_QUOTES, 'UTF-8');

                                        $html .= '<button type="button" class="action-btn preview" title="Previsualizar" 
                        onclick="openPreview(\'' . $safe_path . '\', \'' . $safe_title . '\')">
                        <i class="fa-solid fa-eye"></i>
                      </button>';
                                    }

                                    // --- BOTÓN ELIMINAR (Se mantiene igual) ---
                                    $html .= '<a href="eliminar_video.php?serie=' . $serie . '&t=' . $season . '&e=' . $i . '" 
                     class="action-btn delete" 
                     title="Borrar" 
                     onclick="return confirm(\'¿Estás seguro de eliminar este video?\')">
                     <i class="fa-solid fa-trash"></i>
                  </a>';

                                    $html .= '</span>';
                                    $html .= '</div>';
                                }
                                return $html;
                            }
                            ?>
                        </div>

                        <div class="series-block">
                            <h3 class="series-title">HOUSE OF THE DRAGON</h3>
                            <?php
                            // HotD (Temporada 1, 10 episodios)
                            // Ya no necesitamos simular variables, usamos la función con el mapa de la BD

                            echo '<div class="admin-accordion-block">';
                            echo '<div class="accordion-header"><h2>Temporada 1</h2><span class="accordion-arrow">V</span></div>';
                            echo '<div class="accordion-content episode-list">';
                            echo generate_episodes('HotD', 1, 10, $video_map);
                            echo '</div>';
                            echo '</div>';
                            ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="admin-accordion-block <?php echo $clase_usuarios; ?>" id="acc-usuarios">
            <div class="accordion-header">
                <h2>GESTIONAR USUARIOS</h2>
                <span class="accordion-arrow">V</span>
            </div>
            <div class="accordion-content">
                <form method="GET" class="search-bar">
                    <input type="search" name="search" placeholder="Buscar por ID, nombre, email, país, ciudad o celular..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="action-btn edit">Buscar</button>
                </form>

                <div style="overflow-x:auto;">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>País</th>
                                <th>Ciudad</th>
                                <th>Celular</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $res_usuarios->fetch_assoc()): ?>
                                <?php if ($edit_id == $user['id']): ?>
                                    <form method="POST" action="actualizar_usuario.php">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><input type="text" name="nombre_completo" value="<?= htmlspecialchars($user['nombre_completo']) ?>"></td>
                                            <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
                                            <td><input type="text" name="pais_residencia" value="<?= htmlspecialchars($user['pais_residencia']) ?>"></td>
                                            <td><input type="text" name="ciudad_residencia" value="<?= htmlspecialchars($user['ciudad_residencia']) ?>"></td>
                                            <td><input type="text" name="celular" value="<?= htmlspecialchars($user['celular']) ?>"></td>
                                            <td>
                                                <button type="submit" class="action-btn" style="background:#28a745;"><i class="fa-solid fa-save"></i></button>
                                                <a href="adminPanel.php" class="action-btn delete"><i class="fa-solid fa-xmark"></i></a>
                                            </td>
                                        </tr>
                                    </form>
                                <?php elseif ($reset_id == $user['id']): ?>
                                    <form method="POST" action="actualizar_password.php">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td colspan="5"><input type="password" name="nueva_password" placeholder="Nueva clave para <?= $user['nombre_completo'] ?>..." required></td>
                                            <td>
                                                <button type="submit" class="action-btn" style="background:#28a745;"><i class="fa-solid fa-key"></i></button>
                                                <a href="adminPanel.php" class="action-btn delete"><i class="fa-solid fa-xmark"></i></a>
                                            </td>
                                        </tr>
                                    </form>
                                <?php else: ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['pais_residencia']) ?></td>
                                        <td><?= htmlspecialchars($user['ciudad_residencia']) ?></td>
                                        <td><?= htmlspecialchars($user['codigo_celular'] . " " . $user['celular']) ?></td>
                                        <td>
                                            <a href="adminPanel.php?edit_id=<?= $user['id'] ?>&search=<?= $search ?>" class="action-btn edit"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="adminPanel.php?reset_id=<?= $user['id'] ?>&search=<?= $search ?>" class="action-btn" style="background:#ffc107; color:black;"><i class="fa-solid fa-key"></i></a>
                                            <button class="action-btn delete" onclick="confirmarEliminar(<?= $user['id'] ?>, '<?= $user['nombre_completo'] ?>')"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="admin-accordion-block">
            <div class="accordion-header">
                <h2>
                    HISTORIAL DE COMPRAS
                    <span class="badge-notification"><i class="fa-solid fa-bell"></i> <?php echo $total_compras; ?></span>
                </h2>
                <span class="accordion-arrow">V</span>
            </div>
            <div class="accordion-content">
                <?php if ($res_compras && $res_compras->num_rows > 0): ?>
                    <div style="overflow-x:auto;">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Saga</th>
                                    <th>Temporada</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($compra = $res_compras->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($compra['email']); ?>?subject=¡Gracias por tu compra en Fuego Dragón!&body=Hola <?php echo urlencode($compra['nombre_completo']); ?>,%0D%0A%0D%0AGracias por adquirir la Temporada <?php echo $compra['temporada']; ?> de <?php echo $compra['serie']; ?>. ¡Esperamos que la disfrutes!" 
                                               title="Enviar correo de felicitación" style="color: #f4f4f4; text-decoration: underline;">
                                                <?php echo htmlspecialchars($compra['email']); ?>
                                            </a>
                                            <br>
                                            <small style="color: #8fa0b5;"><?php echo htmlspecialchars($compra['nombre_completo']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($compra['serie']); ?></td>
                                        <td>Temporada <?php echo htmlspecialchars($compra['temporada']); ?></td>
                                        <td><?php echo htmlspecialchars($compra['fecha_compra']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="padding: 20px; text-align: center;">No hay compras registradas aún.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-accordion-block">
            <div class="accordion-header">
                <h2>
                    COMENTARIOS DE USUARIOS
                    <?php if($total_feedback > 0): ?>
                        <span class="badge-notification badge-alert"><i class="fa-solid fa-bell"></i> <?php echo $total_feedback; ?></span>
                    <?php endif; ?>
                </h2>
                <span class="accordion-arrow">V</span>
            </div>

            <div class="accordion-content">
                <?php if ($res_feedback && $res_feedback->num_rows > 0): ?>
                    <?php while ($row = $res_feedback->fetch_assoc()): ?>

                        <div class="feedback-item" style="<?php echo ($row['leido'] == 1) ? 'opacity: 0.5;' : 'border-left: 5px solid #a30000;'; ?>; margin-bottom: 15px; padding: 10px; background: #1e2630; border-radius: 5px;">

                            <div class="feedback-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div class="user-info">
                                    <span class="feedback-user" style="font-weight: bold; color: #fff;">
                                        <?php echo htmlspecialchars($row['nombre_completo']); ?>
                                        <?php if ($row['leido'] == 1): ?> <small style="color: #28a745;">(Leído)</small> <?php endif; ?>
                                    </span>
                                    <br>
                                    <small>
                                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" style="color: #8fa0b5; text-decoration: none;">
                                            <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                                        </a>
                                    </small>
                                </div>
                                <span class="feedback-date" style="font-size: 0.85em; color: #666;">
                                    <?php echo $row['fecha_envio']; ?>
                                </span>
                            </div>

                            <div class="feedback-body" style="margin-top: 10px; color: #ddd;">
                                <p><?php echo nl2br(htmlspecialchars($row['mensaje'])); ?></p>
                            </div>

                            <?php if ($row['leido'] == 0): ?>
                                <form method="POST" style="margin-top: 10px;">
                                    <input type="hidden" name="id_feedback" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="marcar_leido" class="action-btn preview" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;">
                                        <i class="fa-solid fa-check"></i> Marcar como leído
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="padding: 20px; text-align: center;">No hay feedback registrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="previewModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color: rgba(0,0,0,0.9);">
        <div style="position:relative; margin: 5% auto; padding:20px; width:80%; max-width:800px; background:#222; border-radius:10px;">
            <span onclick="closeModal()" style="position:absolute; top:10px; right:20px; color:white; font-size:30px; cursor:pointer;">&times;</span>
            <h3 id="modalTitle" style="color:white; margin-bottom:15px;">Previsualización</h3>
            <video id="videoPlayer" width="100%" controls>
                <source src="" type="video/mp4">
                Tu navegador no soporta videos.
            </video>
        </div>
    </div>
    
    <!-- Overlay de Carga -->
    <div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:2000; color:white; text-align:center; padding-top:20%;">
        <h2>Subiendo video...</h2>
        <p>Por favor no cierres esta ventana, esto puede tardar unos minutos.</p>
        <h2 id="progressText" style="font-size: 3em; margin: 20px 0;">0%</h2>
        <i class="fa-solid fa-spinner fa-spin" style="font-size: 50px;"></i>
    </div>

    <script>
        function openPreview(ruta, titulo) {
        const modal = document.getElementById('previewModal');
        const player = document.getElementById('videoPlayer');
        const title = document.getElementById('modalTitle');
    
        // Cambio sutil: usar load() después de cambiar el src
        player.src = ruta;
        player.load(); // Esto obliga al navegador a cargar el nuevo archivo
        
        title.innerText = titulo;
        modal.style.display = "block";
        player.play();
        }

        function closeModal() {
            const modal = document.getElementById('previewModal');
            const player = document.getElementById('videoPlayer');
            modal.style.display = "none";
            player.pause(); // Detiene el video al cerrar
            player.src = ""; // Limpia la ruta
        }
        
        function startUpload() {
            document.getElementById('loadingOverlay').style.display = 'block';
        }

        function linkExistingVideo(serie, temporada, episodio) {
            let url = prompt("Ingresa la URL del video (Firebase Storage) para el " + serie + " T" + temporada + " E" + episodio + ":");
            if (url && url.trim() !== "") {
                url = url.trim(); // Limpiar espacios en blanco accidentales al copiar/pegar
                // Crear formulario dinámico para enviar la URL a procesar_video.php
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'procesar_video.php';
                
                const fields = {serie, temporada, episodio, firebase_url: url};
                for (const key in fields) {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = key; input.value = fields[key];
                    form.appendChild(input);
                }
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- FIREBASE SDKs (Compat version para facilitar integración) -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-storage.js"></script>

    <script>
        // --- CONFIGURACIÓN DE FIREBASE ---
        // REEMPLAZA ESTOS VALORES CON LOS DE TU PROYECTO EN FIREBASE CONSOLE
        <?php
        // Incluimos el archivo de secretos que NO se sube a GitHub
        // Si el archivo no existe, definimos variables vacías para evitar errores
        if (file_exists('firebase_secrets.php')) {
            include('firebase_secrets.php');
        } else {
            $firebase_apiKey = "FALTA_CONFIGURAR";
            $firebase_authDomain = "";
            $firebase_projectId = "";
            $firebase_storageBucket = "";
            $firebase_messagingSenderId = "";
            $firebase_appId = "";
        }
        ?>
        const firebaseConfig = {
            apiKey: "<?php echo $firebase_apiKey; ?>",
            authDomain: "<?php echo $firebase_authDomain; ?>",
            projectId: "<?php echo $firebase_projectId; ?>",
            storageBucket: "<?php echo $firebase_storageBucket; ?>",
            messagingSenderId: "<?php echo $firebase_messagingSenderId; ?>",
            appId: "<?php echo $firebase_appId; ?>"
        };

        // Validación de seguridad: Verificar si se han reemplazado las credenciales
        if (firebaseConfig.apiKey === "FALTA_CONFIGURAR" || firebaseConfig.apiKey === "") {
            alert("¡ERROR DE CONFIGURACIÓN!\n\nNo has reemplazado las credenciales de Firebase en el archivo adminPanel.php.\n\nPor favor edita el código y pon tus datos reales del proyecto Firebase.");
        }

        // Inicializar Firebase
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        function uploadToFirebase(inputElement) {
            const file = inputElement.files[0];
            if (!file) return;

            // Mostrar overlay de carga
            document.getElementById('loadingOverlay').style.display = 'block';

            // Obtener metadatos del formulario
            const form = inputElement.form;
            const serie = form.serie.value;
            const temporada = form.temporada.value;
            const episodio = form.episodio.value;

            // Crear referencia en Storage: videos/Serie/T1/E1_nombre.mp4
            const path = `videos/${serie}/t${temporada}/e${episodio}_${file.name}`;
            const storageRef = firebase.storage().ref().child(path);
            const uploadTask = storageRef.put(file);

            uploadTask.on('state_changed', 
                (snapshot) => {
                    // Progreso (opcional: podrías mostrar el % en el overlay)
                    var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                    console.log('Subida: ' + progress + '%');
                    document.getElementById('progressText').innerText = Math.floor(progress) + '%';
                }, 
                (error) => {
                    alert("Error al subir a Firebase: " + error.code + " - " + error.message);
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 
                () => {
                    // Subida completada con éxito
                    uploadTask.snapshot.ref.getDownloadURL().then((downloadURL) => {
                        // 1. Crear input oculto con la URL
                        const urlInput = document.createElement('input');
                        urlInput.type = 'hidden';
                        urlInput.name = 'firebase_url';
                        urlInput.value = downloadURL;
                        form.appendChild(urlInput);

                        // 2. Quitar el nombre del input file para que NO se envíe el archivo físico al servidor PHP
                        inputElement.removeAttribute('name');

                        // 3. Enviar el formulario a procesar_video.php
                        form.submit();
                    });
                }
            );
        }
    </script>
</body>

</html>