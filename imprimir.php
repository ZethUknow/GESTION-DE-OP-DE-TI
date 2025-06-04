<?php
include 'db.php';

$conn = openConnection();

if (!isset($_GET['id'])) {
    echo "ID de incidente no especificado.";
    exit();
}

$id_incidente = $_GET['id'];

$query = $conn->prepare("
    SELECT i.*, t.nombre AS tecnico_nombre, ci.nombre AS ci_nombre, ci.procesador, ci.ram, ci.display, ci.responsable AS ci_responsable,
           u.nombre AS encargado_nombre, u.sucursal AS sucursal_nombre 
    FROM incidentes i
    LEFT JOIN tecnicos t ON i.id_tecnico_asignado = t.id_tecnico
    LEFT JOIN ci_items ci ON i.id_ci_afectado = ci.id_ci
    LEFT JOIN usuarios u ON i.responsable = u.nombre
    WHERE i.id_incidente = ?
");
$query->bind_param("i", $id_incidente);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "Incidente no encontrado.";
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Incidente</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #999; padding: 8px; }
        .no-border { border: none; }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px 0 0;
            font-size: 16px;
            text-decoration: none;
            color: #000;
            background-color: #ffdd57;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #f0c83c;
        }

        @media print {
            .btn { display: none; }
        }
    </style>
</head>
<body>
    <h2>Detalles del Incidente</h2>

    <button onclick="window.print()" class="btn">Imprimir</button>

    <table>
        <tr><th>ID</th><td><?= htmlspecialchars($row['id_incidente']) ?></td></tr>
        <tr><th>Título</th><td><?= htmlspecialchars($row['titulo']) ?></td></tr>
        <tr><th>Descripción</th><td><?= nl2br(htmlspecialchars($row['descripcion'])) ?></td></tr>
        <tr><th>Estado</th><td><?= htmlspecialchars($row['estado']) ?></td></tr>
        <tr><th>Prioridad</th><td><?= htmlspecialchars($row['prioridad']) ?></td></tr>
        <tr><th>Fecha de Reporte</th><td><?= htmlspecialchars($row['fecha_reporte']) ?></td></tr>
        <tr><th>Fecha Límite</th><td><?= $row['fecha_limite'] && $row['fecha_limite'] != '0000-00-00' ? htmlspecialchars($row['fecha_limite']) : 'No asignada' ?></td></tr>
        <tr><th>Técnico Asignado</th><td><?= htmlspecialchars($row['tecnico_nombre'] ?: 'No asignado') ?></td></tr>
        <tr><th>Encargado</th><td><?= htmlspecialchars($row['encargado_nombre']) ?> (<?= htmlspecialchars($row['sucursal_nombre']) ?>)</td></tr>
        <tr><th>CI Afectado</th><td><?= htmlspecialchars($row['ci_nombre']) ?></td></tr>
        <tr><th>Responsable del CI</th><td><?= htmlspecialchars($row['ci_responsable']) ?></td></tr>
        <tr><th>Procesador</th><td><?= htmlspecialchars($row['procesador']) ?></td></tr>
        <tr><th>RAM</th><td><?= htmlspecialchars($row['ram']) ?></td></tr>
        <tr><th>Display</th><td><?= htmlspecialchars($row['display']) ?></td></tr>
        <tr><th>Resolución</th><td><?= nl2br(htmlspecialchars($row['resolucion'])) ?: 'Sin resolución registrada' ?></td></tr>
    </table>
</body>
</html>

<?php $conn->close(); ?>
