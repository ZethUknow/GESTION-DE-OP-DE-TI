<?php
include 'db.php';
$conn = openConnection();

$id_incidente = isset($_GET['id']) ? intval($_GET['id']) : 0;

$asignados = $conn->prepare("
    SELECT si.*, cs.nombre_servicio 
    FROM servicios_incidente si 
    JOIN catalogo_servicios cs ON si.id_servicio = cs.id_servicio 
    WHERE si.id_incidente = ?
");
$asignados->bind_param("i", $id_incidente);
$asignados->execute();
$servicios_result = $asignados->get_result();

if ($servicios_result->num_rows > 0): ?>
    <ul>
        <?php while ($row = $servicios_result->fetch_assoc()): ?>
            <li><strong><?= htmlspecialchars($row['nombre_servicio']) ?></strong> (<?= htmlspecialchars($row['fecha_registro']) ?>)</li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No hay servicios asignados aún.</p>
<?php endif; ?>
