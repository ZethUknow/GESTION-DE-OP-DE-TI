<?php
include 'db.php';
$conn = openConnection();

$query = $_GET['query'] ?? '';
$query = "%$query%";

$stmt = $conn->prepare("SELECT id_recurso, nombre_recurso, cantidad_disponible FROM catalogo_recursos WHERE nombre_recurso LIKE ? AND estado = 'activo'");
$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();

$recursos = [];
while ($row = $result->fetch_assoc()) {
    $recursos[] = $row;
}

echo json_encode($recursos);
