<?php
session_start();
include("../conexion/conexion.php");

// 1. Verificar si el admin está logueado
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

// 2. Verificar que lleguen los parámetros necesarios
if (isset($_GET['serie']) && isset($_GET['t']) && isset($_GET['e'])) {
    $serie = $_GET['serie'];
    $temporada = $_GET['t'];
    $episodio = $_GET['e'];

    // 3. Obtener información del video antes de borrar (para saber si es local)
    $sql = "SELECT ruta_archivo FROM videos WHERE serie = ? AND temporada = ? AND episodio = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sii", $serie, $temporada, $episodio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        $ruta = $fila['ruta_archivo'];

        // Si es archivo local (no es URL de Firebase), intentar borrarlo del disco del servidor
        if (strpos($ruta, 'http') !== 0) {
            $carpeta = ($serie == 'GoT') ? 'got' : 'hotd';
            $ruta_fisica = "../activos/videos/" . $carpeta . "/" . $ruta;
            
            if (file_exists($ruta_fisica)) {
                unlink($ruta_fisica); // Borra el archivo físico local
            }
        }
        // Nota: Si es un video de Firebase, este script solo borra la referencia en la BD.
        // El archivo en el bucket permanecerá intacto (lo cual es seguro).
    }

    // 4. Borrar el registro de la base de datos
    $sql_delete = "DELETE FROM videos WHERE serie = ? AND temporada = ? AND episodio = ?";
    $stmt_delete = $conexion->prepare($sql_delete);
    $stmt_delete->bind_param("sii", $serie, $temporada, $episodio);

    if ($stmt_delete->execute()) {
        header("Location: adminPanel.php?status=success&msg=Video+desvinculado+correctamente");
    } else {
        header("Location: adminPanel.php?status=error&msg=Error+al+eliminar+de+la+BD");
    }
} else {
    header("Location: adminPanel.php?status=error&msg=Datos+incompletos");
}
?>