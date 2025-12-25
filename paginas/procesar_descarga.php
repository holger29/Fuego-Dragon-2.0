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

    // 3. Redirigir al archivo para iniciar la descarga
    if (strpos($ruta_db, 'http') === 0) {
        // Si es Firebase, redirigimos directamente
        header("Location: " . $ruta_db);
    } else {
        // Si es local, construimos la ruta
        $folder = ($serie == 'GoT') ? 'got' : 'hotd';
        $file_path = "../activos/videos/" . $folder . "/" . $ruta_db;
        
        // Redirección simple al archivo local
        header("Location: " . $file_path);
    }
    exit();

} else {
    echo "<script>alert('Error: Video no encontrado.'); window.history.back();</script>";
}
?>