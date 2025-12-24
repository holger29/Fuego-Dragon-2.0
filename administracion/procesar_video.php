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

// 2. Verificar si se recibió un archivo O una URL de Firebase
if (isset($_FILES['video_file']) || isset($_POST['firebase_url'])) {
    $serie = $_POST['serie'];       // 'GoT' o 'HotD'
    $temporada = $_POST['temporada'];
    $episodio = $_POST['episodio'];
    
    $ruta_archivo_final = null;

    // --- OPCIÓN A: URL DE FIREBASE ---
    if (isset($_POST['firebase_url']) && !empty($_POST['firebase_url'])) {
        $ruta_archivo_final = $_POST['firebase_url'];
    } 
    // --- OPCIÓN B: ARCHIVO LOCAL (Fallback) ---
    elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === 0) {
        $file = $_FILES['video_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];

        // 3. Definir la ruta de destino (Storage Local)
        $folderName = ($serie == 'GoT') ? 'got' : 'hotd';
        $destDir = "../activos/videos/" . $folderName . "/";

        // Crear la carpeta si no existe
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        // Nombre final del archivo: serie_t1_e1_nombreoriginal.mp4
        $finalFileName = strtolower($serie) . "_t" . $temporada . "_e" . $episodio . "_" . $fileName;
        $destPath = $destDir . $finalFileName;

        if (move_uploaded_file($fileTmpName, $destPath)) {
            $ruta_archivo_final = $finalFileName;
        } else {
            header("Location: adminPanel.php?status=error&msg=Error+al+mover+archivo+(Permisos)");
            exit();
        }
    }

    // 5. Registrar en la Base de Datos si tenemos una ruta válida
    if ($ruta_archivo_final) {
            $sql = "INSERT INTO videos (serie, temporada, episodio, titulo, ruta_archivo, es_gratis) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE ruta_archivo = VALUES(ruta_archivo)";
            
            $stmt = $conexion->prepare($sql);
            $titulo = "Capítulo " . $episodio;
            
            // Lógica de los 4 capítulos gratis (Regla de negocio 2.2)
            $es_gratis = ($serie == 'GoT' && $temporada == 1 && $episodio <= 4) ? 1 : 0;

            $stmt->bind_param("siissi", $serie, $temporada, $episodio, $titulo, $ruta_archivo_final, $es_gratis);
            
            if ($stmt->execute()) {
                // Éxito: Regresamos al panel con un mensaje
                header("Location: adminPanel.php?status=success&msg=Video+subido+correctamente");
            } else {
                header("Location: adminPanel.php?status=error&msg=Error+BD:+" . urlencode($conexion->error));
            }
    } else {
        // Si llegamos aquí, no hubo URL de firebase ni archivo válido
        header("Location: adminPanel.php?status=error&msg=Error+de+subida+o+archivo+no+valido");
    }
} else {
    header("Location: adminPanel.php?status=error&msg=No+se+recibio+archivo");
}
?>