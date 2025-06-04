<?php
include 'db.php';
$conn = openConnection();

$id_incidente = intval($_POST['id_incidente'] ?? 0);
$id_servicio = intval($_POST['id_servicio'] ?? 0);

if ($id_incidente <= 0 || $id_servicio <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    exit();
}

// Verificar si ya fue asignado ese servicio al incidente
$check = $conn->prepare("SELECT * FROM servicios_incidente WHERE id_incidente = ? AND id_servicio = ?");
$check->bind_param("ii", $id_incidente, $id_servicio);
$check->execute();
$check_res = $check->get_result();

if ($check_res->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Este servicio ya fue asignado al incidente.']);
    exit();
}

// Asignar servicio
$insert = $conn->prepare("INSERT INTO servicios_incidente (id_incidente, id_servicio, fecha_registro) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $id_incidente, $id_servicio);
$insert->execute();

// Calcular tiempo total estimado de servicios para el incidente
$calc = $conn->prepare("
    SELECT SUM(cs.tiempo_estimado_minutos) AS total 
    FROM servicios_incidente si 
    JOIN catalogo_servicios cs ON si.id_servicio = cs.id_servicio 
    WHERE si.id_incidente = ?
");
$calc->bind_param("i", $id_incidente);
$calc->execute();
$res = $calc->get_result();
$total = $res->fetch_assoc()['total'] ?? 0;

// Actualizar tiempo estimado total en incidente
$update = $conn->prepare("UPDATE incidentes SET tiempo_estimado_total = ? WHERE id_incidente = ?");
$update->bind_param("ii", $total, $id_incidente);
$update->execute();

echo json_encode(['success' => true]);
$conn->close();
