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

// Clases automáticas para que los acordeones se mantengan abiertos según la acción
$clase_usuarios = ($edit_id || $reset_id || $status || !empty($search)) ? 'active' : '';

// 3. Consulta Usuarios con BUSCADOR MULTICRITERIO
$sql_usuarios = "SELECT * FROM usuarios WHERE id LIKE '%$search%' OR nombre_completo LIKE '%$search%' OR email LIKE '%$search%' OR pais_residencia LIKE '%$search%' OR ciudad_residencia LIKE '%$search%' OR celular LIKE '%$search%' ORDER BY id DESC";
$res_usuarios = $conexion->query($sql_usuarios);

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
       /*document.addEventListener('DOMContentLoaded', function() {
            // Script para manejar TODOS los acordeones
            const accordionHeaders = document.querySelectorAll('.accordion-header');

            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.admin-accordion-block');
                    block.classList.toggle('active');
                });
            });
        });*/
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
            activeBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
                            // Función auxiliar para generar las listas de episodios (solo frontend)
                            function generate_episodes($season, $count, $available_until = 0)
                            {
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
                <h2>COMENTARIOS DE USUARIOS</h2>
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
</body>

</html>