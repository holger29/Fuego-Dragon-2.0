<?php
session_start();
include("../conexion/conexion.php");

// 1. Validar que el admin esté logueado
if (!isset($_SESSION['admin_id'])) {
    die("Acceso denegado.");
}

// Verificar si el archivo excede el tamaño máximo de POST (error común en videos)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_FILES) && empty($_POST)) {
    header("Location: adminPanel.php?status=error&msg=El+archivo+es+demasiado+grande+(Revisa+post_max_size+en+php.ini)");
    exit();
}

// 2. Verificar si se recibió un archivo
if (isset($_FILES['video_file'])) {
    $serie = $_POST['serie'];       // 'GoT' o 'HotD'
    $temporada = $_POST['temporada'];
    $episodio = $_POST['episodio'];
    
    $file = $_FILES['video_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];

    // 3. Definir la ruta de destino (Storage Local)
    // Según tu estructura: /activos/videos/got/ o /activos/videos/hotd/
    $folderName = ($serie == 'GoT') ? 'got' : 'hotd';
    $destDir = "../activos/videos/" . $folderName . "/";

    // Crear la carpeta si no existe
    if (!is_dir($destDir)) {
        mkdir($destDir, 0777, true);
    }

    // Nombre final del archivo: serie_t1_e1_nombreoriginal.mp4
    $finalFileName = strtolower($serie) . "_t" . $temporada . "_e" . $episodio . "_" . $fileName;
    $destPath = $destDir . $finalFileName;

    if ($fileError === 0) {
        // 4. Mover el video de la USB/Temporal a tu carpeta de proyecto
        if (move_uploaded_file($fileTmpName, $destPath)) {
            
            // 5. Registrar en la Base de Datos
            // Si el registro ya existe, lo actualizamos; si no, lo insertamos.
            $sql = "INSERT INTO videos (serie, temporada, episodio, titulo, ruta_archivo, es_gratis) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE ruta_archivo = VALUES(ruta_archivo)";
            
            $stmt = $conexion->prepare($sql);
            $titulo = "Capítulo " . $episodio;
            
            // Lógica de los 4 capítulos gratis (Regla de negocio 2.2)
            $es_gratis = ($serie == 'GoT' && $temporada == 1 && $episodio <= 4) ? 1 : 0;

            $stmt->bind_param("siissi", $serie, $temporada, $episodio, $titulo, $finalFileName, $es_gratis);
            
            if ($stmt->execute()) {
                // Éxito: Regresamos al panel con un mensaje
                header("Location: adminPanel.php?status=success&msg=Video+subido+correctamente");
            } else {
                header("Location: adminPanel.php?status=error&msg=Error+BD:+" . urlencode($conexion->error));
            }
        } else {
            header("Location: adminPanel.php?status=error&msg=Error+al+mover+archivo+(Permisos)");
        }
    } else {
        header("Location: adminPanel.php?status=error&msg=Error+de+subida+Codigo+" . $fileError);
    }
} else {
    header("Location: adminPanel.php?status=error&msg=No+se+recibio+archivo");
}
?>