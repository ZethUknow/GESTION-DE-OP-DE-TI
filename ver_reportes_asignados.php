<?php
session_start();
include 'db.php';

$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'tecnico') {
    header("Location: login.php");
    exit();
}

$id_tecnico = $_SESSION['id_tecnico'];

$stmt = $conn->prepare("SELECT * FROM incidentes WHERE id_tecnico_asignado = ? AND estado = 'en proceso'");
$stmt->bind_param("i", $id_tecnico);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Asignados</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="dashboard_tecnico.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="ver_reportes_asignados.php" class="active">Ver Reportes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>
<main class="main-container">
    <h1 class="page-title">Reportes Asignados</h1>
    <div class="table-container">
        <table class="reportes-table" border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Fecha de Reporte</th>
                    <th>Tiempo Estimado (min)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()):
                    $id_incidente = $row['id_incidente'];
                    $tiene_diagnostico = !empty($row['diagnostico']);

                    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM servicios_incidente WHERE id_incidente = ?");
                    $stmt2->bind_param("i", $id_incidente);
                    $stmt2->execute();
                    $stmt2->bind_result($total_servicios);
                    $stmt2->fetch();
                    $stmt2->close();

                    $stmt3 = $conn->prepare("SELECT COUNT(*) FROM solicitudes_recursos WHERE id_incidente = ? AND estado = 'pendiente'");
                    $stmt3->bind_param("i", $id_incidente);
                    $stmt3->execute();
                    $stmt3->bind_result($pendientes_recursos);
                    $stmt3->fetch();
                    $stmt3->close();

                    $stmt4 = $conn->prepare("SELECT COUNT(*) FROM solicitudes_nuevo_servicio WHERE estado = 'pendiente' AND id_tecnico = ?");
                    $stmt4->bind_param("i", $id_tecnico);
                    $stmt4->execute();
                    $stmt4->bind_result($pendientes_servicios);
                    $stmt4->fetch();
                    $stmt4->close();

                    $puede_terminar = $tiene_diagnostico && $total_servicios > 0 && $pendientes_recursos == 0 && $pendientes_servicios == 0;
                ?>
                <tr>
                    <td><?= $id_incidente ?></td>
                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                    <td><?= htmlspecialchars($row['prioridad']) ?></td>
                    <td><?= $row['fecha_reporte'] ?></td>
                    <td><?= htmlspecialchars($row['tiempo_estimado_total'] ?? '-') ?></td>
                    <td>
                        <a href="atender_incidente.php?id=<?= $id_incidente ?>">Atender</a>
                        <?php if ($puede_terminar): ?>
                            <a href="terminar_incidente.php?id=<?= $id_incidente ?>" style="background-color:#28a745;">Terminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8">No tienes reportes asignados en proceso.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
