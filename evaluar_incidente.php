<?php
session_start();
include 'db.php';

$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'encargado') {
    header("Location: login.php");
    exit();
}

$nombre_encargado = $_SESSION['nombre_usuario'] ?? '';  // Verifica que esta variable coincida con el campo 'responsable'
$id_incidente = intval($_POST['id_incidente'] ?? 0);
$estrellas = intval($_POST['estrellas'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($id_incidente <= 0 || $estrellas < 1 || $estrellas > 5) {
    echo "Datos inválidos.";
    exit();
}

// Verificar que el incidente pertenezca al encargado y esté en estado 'terminado'
$stmt = $conn->prepare("SELECT id_tecnico_asignado FROM incidentes WHERE id_incidente = ? AND LOWER(responsable) = LOWER(?) AND estado = 'terminado'");
$stmt->bind_param("is", $id_incidente, $nombre_encargado);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No autorizado para evaluar este incidente.";
    exit();
}

$incidente = $res->fetch_assoc();
$id_tecnico = $incidente['id_tecnico_asignado'];

// Actualizar incidente con evaluación
$update = $conn->prepare("UPDATE incidentes SET estado = 'evaluado', estrellas = ?, comentario_encargado = ? WHERE id_incidente = ?");
$update->bind_param("isi", $estrellas, $comentario, $id_incidente);
$update->execute();

// Calcular nuevo promedio de desempeño del técnico
$avg_stmt = $conn->prepare("SELECT AVG(estrellas) AS promedio FROM incidentes WHERE id_tecnico_asignado = ? AND estado = 'evaluado' AND estrellas IS NOT NULL");
$avg_stmt->bind_param("i", $id_tecnico);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$promedio = $avg_result->fetch_assoc()['promedio'] ?? 0;

// Actualizar campo de desempeño en la tabla tecnicos
$upd_tecnico = $conn->prepare("UPDATE tecnicos SET desempeno = ? WHERE id_tecnico = ?");
$upd_tecnico->bind_param("di", $promedio, $id_tecnico);
$upd_tecnico->execute();

$conn->close();
header("Location: Ver_Reportes.php?mensaje=Evaluación+registrada+con+éxito");
exit();
?>
