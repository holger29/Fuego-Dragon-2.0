<?php
session_start();
include("../conexion/conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../autenticacion/login.php");
    exit();
}

// Validar parámetros
if (!isset($_GET['serie']) || !isset($_GET['t']) || !isset($_GET['e'])) {
    header("Location: dashboard.php");
    exit();
}

$serie = $_GET['serie'];
$temp = $_GET['t'];
$ep = $_GET['e'];
$usuario_id = $_SESSION['usuario_id'];

// 1. Obtener la ruta del video
$sql = "SELECT ruta_archivo, titulo FROM videos WHERE serie = ? AND temporada = ? AND episodio = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sii", $serie, $temp, $ep);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $ruta_db = $row['ruta_archivo'];
    $titulo = $row['titulo'];

    // Determinar ruta final (Firebase o Local)
    if (strpos($ruta_db, 'http') === 0) {
        $video_src = $ruta_db;
    } elseif (strpos($ruta_db, 'gs://') === 0) {
        // Convertir formato gs:// a URL pública HTTP
        $temp_path = substr($ruta_db, 5); // Quitar gs://
        $parts = explode('/', $temp_path, 2);
        if (count($parts) == 2) {
            $bucket = $parts[0];
            $file_path = rawurlencode($parts[1]); // Codificar ruta
            $video_src = "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o/{$file_path}?alt=media";
        } else {
            $video_src = $ruta_db;
        }
    } else {
        $folder = ($serie == 'GoT') ? 'got' : 'hotd';
        $video_src = "../activos/videos/" . $folder . "/" . $ruta_db;
    }

    // 2. REGISTRAR HISTORIAL DE VISUALIZACIÓN
    // Insertamos un nuevo registro cada vez que ve el video
    $sql_hist = "INSERT INTO historial_vistas (usuario_id, serie, temporada, episodio) VALUES (?, ?, ?, ?)";
    $stmt_hist = $conexion->prepare($sql_hist);
    $stmt_hist->bind_param("isii", $usuario_id, $serie, $temp, $ep);
    $stmt_hist->execute();

} else {
    echo "<script>alert('Video no disponible aún.'); window.history.back();</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproduciendo: <?php echo $serie . " T" . $temp . " E" . $ep; ?></title>
    <link rel="stylesheet" href="../activos/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #000; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; overflow: hidden; }
        .player-container { width: 90%; max-width: 1000px; }
        video { width: 100%; border-radius: 8px; box-shadow: 0 0 20px rgba(163, 0, 0, 0.5); outline: none; }
        .controls { margin-top: 20px; text-align: left; width: 100%; display: flex; justify-content: space-between; align-items: center; }
        .back-btn { color: #ccc; text-decoration: none; font-size: 1.1em; display: flex; align-items: center; gap: 10px; transition: color 0.3s; }
        .back-btn:hover { color: #a30000; }
        h2 { margin: 0; font-size: 1.2em; color: #f4f4f4; }
        small { color: #888; }
    </style>
</head>
<body>
    <div class="player-container">
        <!-- controlsList="nodownload" oculta el botón de descarga nativo del navegador -->
        <video controls autoplay controlsList="nodownload">
            <source src="<?php echo htmlspecialchars($video_src); ?>" type="video/mp4">
            Tu navegador no soporta la reproducción de video.
        </video>
        
        <div class="controls">
            <div>
                <h2><?php echo $serie; ?> - Temporada <?php echo $temp; ?> Episodio <?php echo $ep; ?></h2>
                <small><?php echo htmlspecialchars($titulo); ?></small>
            </div>
            <a href="<?php echo ($serie == 'GoT') ? 'page_GoT.php' : 'page_HotD.php'; ?>" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Volver a la Serie
            </a>
        </div>
    </div>
</body>
</html>