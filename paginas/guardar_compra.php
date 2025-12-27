<?php
session_start();
include("../conexion/conexion.php");

// Responder siempre en formato JSON
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Leer los datos JSON enviados por JavaScript
$input = json_decode(file_get_contents('php://input'), true);

$usuario_id = $_SESSION['usuario_id'];
$serie = $input['serie'];
$temporada = $input['temporada'];
$episodio = $input['episodio'] ?? 0; // 0 si es compra de temporada
$tipo = $input['tipo'] ?? 'temporada'; // 'temporada' o 'descarga'
$monto = $input['monto'];
$orderID = $input['orderID']; // ID de transacción de PayPal

// Verificar duplicados SOLO si es compra de temporada (las descargas se cobran siempre)
if ($tipo === 'temporada') {
    $check = "SELECT id FROM compras WHERE usuario_id = ? AND serie = ? AND temporada = ? AND tipo_compra = 'temporada'";
    $stmt_check = $conexion->prepare($check);
    $stmt_check->bind_param("isi", $usuario_id, $serie, $temporada);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Ya adquirido previamente']);
        exit();
    }
}

$sql = "INSERT INTO compras (usuario_id, serie, temporada, episodio, tipo_compra, monto, metodo_pago, referencia_pago) VALUES (?, ?, ?, ?, ?, ?, 'PayPal', ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("isiisds", $usuario_id, $serie, $temporada, $episodio, $tipo, $monto, $orderID);

if ($stmt->execute()) {
    // Si es descarga, autorizamos la sesión para que procesar_descarga.php permita el acceso
    if ($tipo === 'descarga') {
        $_SESSION['descarga_autorizada'] = [
            'serie' => $serie,
            't' => $temporada,
            'e' => $episodio
        ];
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conexion->error]);
}
?>