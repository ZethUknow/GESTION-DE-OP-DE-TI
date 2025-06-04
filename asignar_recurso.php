<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php';
$conn = openConnection();

if ($_SESSION['tipo_usuario'] !== 'tecnico') {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$id_tecnico = $_SESSION['id_tecnico'];
$id_incidente = intval($_POST['id_incidente'] ?? 0);
$id_recurso = intval($_POST['id_recurso'] ?? 0);
$cantidad = intval($_POST['cantidad'] ?? 1);

if ($id_incidente <= 0 || $id_recurso <= 0 || $cantidad <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

// Verifica si ya hay una solicitud pendiente igual
$check = $conn->prepare("SELECT id_solicitud FROM solicitudes_recursos WHERE id_incidente = ? AND id_recurso = ? AND estado = 'pendiente'");
$check->bind_param("ii", $id_incidente, $id_recurso);
$check->execute();
$resCheck = $check->get_result();

if ($resCheck->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Ya existe una solicitud pendiente para este recurso.']);
    exit;
}

// Insertar solicitud en tabla
$stmt = $conn->prepare("INSERT INTO solicitudes_recursos (id_tecnico, id_incidente, id_recurso, cantidad_solicitada) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $id_tecnico, $id_incidente, $id_recurso, $cantidad);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Solicitud registrada correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar la solicitud']);
}
?>
