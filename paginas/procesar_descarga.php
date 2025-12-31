<?php
session_start();
include("../conexion/conexion.php");

if (!isset($_SESSION['usuario_id']) || !isset($_GET['serie'])) {
    header("Location: dashboard.php");
    exit();
}

$serie = $_GET['serie'];
$temp = $_GET['t'];
$ep = $_GET['e'];
$usuario_id = $_SESSION['usuario_id'];

// --- VERIFICACIÓN DE PAGO ---
// Verificamos si existe la autorización creada por guardar_compra.php
if (isset($_SESSION['descarga_autorizada'])) {
    $auth = $_SESSION['descarga_autorizada'];
    // Validar que la autorización coincida con el video solicitado
    if ($auth['serie'] == $serie && $auth['t'] == $temp && $auth['e'] == $ep) {
        unset($_SESSION['descarga_autorizada']); // Consumir la autorización (un solo uso)
    } else {
        echo "<script>alert('Error de autorización. Por favor intenta pagar nuevamente.'); window.location.href='dashboard.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Esta descarga requiere pago ($1 USD para GoT, $2 USD para HotD).'); window.history.back();</script>";
    exit();
}

// 1. Obtener la ruta del video
$sql = "SELECT ruta_archivo FROM videos WHERE serie = ? AND temporada = ? AND episodio = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sii", $serie, $temp, $ep);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $ruta_db = $row['ruta_archivo'];

    // 2. REGISTRAR HISTORIAL DE DESCARGA (Contenido Adquirido)
    $sql_hist = "INSERT INTO historial_descargas (usuario_id, serie, temporada, episodio) VALUES (?, ?, ?, ?)";
    $stmt_hist = $conexion->prepare($sql_hist);
    $stmt_hist->bind_param("isii", $usuario_id, $serie, $temp, $ep);
    $stmt_hist->execute();

    // 3. Obtener la URL del video para mostrar en el reproductor
    $video_url = "";
    if (strpos($ruta_db, 'http') === 0) {
        $video_url = $ruta_db;
    } elseif (strpos($ruta_db, 'gs://') === 0) {
        $temp_path = substr($ruta_db, 5);
        $parts = explode('/', $temp_path, 2);
        if (count($parts) == 2) {
            $bucket = $parts[0];
            $file_path = rawurlencode($parts[1]);
            $video_url = "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o/{$file_path}?alt=media";
        } else {
            echo "<script>alert('Error: Formato de ruta inválido.'); window.history.back();</script>";
            exit();
        }
    } else {
        $folder = ($serie == 'GoT') ? 'got' : 'hotd';
        $video_url = "../activos/videos/" . $folder . "/" . $ruta_db;
    }
} else {
    echo "<script>alert('Error: Video no encontrado.'); window.history.back();</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar Video | Fuego Dragón</title>
    <link rel="stylesheet" href="../activos/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #121417; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; text-align: center; }
        .download-container { width: 90%; max-width: 800px; background: #1a1a1a; padding: 20px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        video { width: 100%; border-radius: 8px; margin-bottom: 15px; outline: none; }
        .instructions { background-color: #2a2a2a; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745; text-align: left; }
        .instructions p { margin: 0; color: #ccc; font-size: 0.95em; line-height: 1.5; }
        .btn-dashboard { display: inline-block; padding: 10px 20px; background-color: #a30000; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s; }
        .btn-dashboard:hover { background-color: #800000; }
    </style>
</head>
<body>
    <div class="download-container">
        <h2 style="color: #f4f4f4; margin-bottom: 20px;">Vista de Descarga</h2>
        
        <video controls autoplay>
            <source src="<?php echo htmlspecialchars($video_url); ?>" type="video/mp4">
            Tu navegador no soporta la reproducción de video.
        </video>

        <div class="instructions">
            <p><i class="fa-solid fa-circle-info" style="color: #28a745;"></i> <strong>Instrucciones:</strong> Para descargar el video, click en los tres punticos (menú) del reproductor, click en la opción <strong>Descargar</strong>, luego aparecerá el explorador de archivos, eliges una carpeta y click en <strong>Guardar</strong>.</p>
        </div>

        <a href="dashboard.php" class="btn-dashboard"><i class="fa-solid fa-house"></i> Volver al Dashboard</a>
    </div>
</body>
</html>