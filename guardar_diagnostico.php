<?php
session_start();
include 'db.php';

$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'tecnico') {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$id_incidente = intval($_POST['id_incidente'] ?? 0);
$diagnostico = trim($_POST['diagnostico'] ?? '');

if ($id_incidente <= 0 || empty($diagnostico)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Actualizar el diagnóstico en la tabla incidentes
$stmt = $conn->prepare("UPDATE incidentes SET diagnostico = ? WHERE id_incidente = ?");
$stmt->bind_param("si", $diagnostico, $id_incidente);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Diagnóstico asignado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar diagnóstico']);
}
?>
