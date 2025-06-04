<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Procesar acciones de servicios
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'], $_POST['id_solicitud'])) {
    $id = intval($_POST['id_solicitud']);
    $accion = $_POST['accion'];

    if ($accion === 'aceptar') {
        $stmt = $conn->prepare("SELECT nombre_servicio, descripcion, costo_sugerido, tiempo_estimado_minutos FROM solicitudes_nuevo_servicio WHERE id_solicitud = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $data = $res->fetch_assoc();
            $insert = $conn->prepare("INSERT INTO catalogo_servicios (nombre_servicio, descripcion, costo, tiempo_estimado_minutos) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssdi", $data['nombre_servicio'], $data['descripcion'], $data['costo_sugerido'], $data['tiempo_estimado_minutos']);
            $insert->execute();
            $update = $conn->prepare("UPDATE solicitudes_nuevo_servicio SET estado = 'aprobado' WHERE id_solicitud = ?");
            $update->bind_param("i", $id);
            $update->execute();
        }
    } elseif ($accion === 'rechazar') {
        $update = $conn->prepare("UPDATE solicitudes_nuevo_servicio SET estado = 'rechazado' WHERE id_solicitud = ?");
        $update->bind_param("i", $id);
        $update->execute();
    }
}

// Procesar acciones de recursos
if (isset($_POST['accion_recurso'], $_POST['id_solicitud'])) {
    $id = intval($_POST['id_solicitud']);
    $accion = $_POST['accion_recurso'];

    if ($accion === 'aceptar') {
        $rstmt = $conn->prepare(
            "SELECT sr.id_recurso, sr.cantidad_solicitada, cr.nombre_recurso, cr.descripcion, sr.id_incidente
             FROM solicitudes_recursos sr
             JOIN catalogo_recursos cr ON sr.id_recurso = cr.id_recurso
             WHERE sr.id_solicitud = ?"
        );
        $rstmt->bind_param("i", $id);
        $rstmt->execute();
        $res = $rstmt->get_result();

        if ($res->num_rows > 0) {
            $data = $res->fetch_assoc();

            $insert = $conn->prepare("INSERT INTO catalogo_recursos (nombre_recurso, descripcion, cantidad_disponible, unidad) VALUES (?, ?, ?, 'pieza')");
            $insert->bind_param("ssi", $data['nombre_recurso'], $data['descripcion'], $data['cantidad_solicitada']);
            $insert->execute();

            $id_recurso_nuevo = $conn->insert_id;

            $insert_rel = $conn->prepare("INSERT INTO recursos_incidente (id_incidente, id_recurso, cantidad_utilizada) VALUES (?, ?, ?)");
            $insert_rel->bind_param("iii", $data['id_incidente'], $id_recurso_nuevo, $data['cantidad_solicitada']);
            $insert_rel->execute();

            $update = $conn->prepare("UPDATE solicitudes_recursos SET estado = 'aprobado' WHERE id_solicitud = ?");
            $update->bind_param("i", $id);
            $update->execute();
        }
    } elseif ($accion === 'rechazar') {
        $update = $conn->prepare("UPDATE solicitudes_recursos SET estado = 'rechazado' WHERE id_solicitud = ?");
        $update->bind_param("i", $id);
        $update->execute();
    }
}

// Procesar acciones de base de conocimiento
if (isset($_POST['accion_bc'], $_POST['id_solicitud_bc'])) {
    $id = intval($_POST['id_solicitud_bc']);
    $accion = $_POST['accion_bc'];

    if ($accion === 'aceptar') {
        $stmt = $conn->prepare("SELECT id_tecnico, error, solucion FROM solicitudes_BC WHERE id_solicitud = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $data = $res->fetch_assoc();
            $insert = $conn->prepare("INSERT INTO Base_De_Conocimiento (error, solucion, id_tecnico, fecha_registro) VALUES (?, ?, ?, NOW())");
            $insert->bind_param("ssi", $data['error'], $data['solucion'], $data['id_tecnico']);
            $insert->execute();
            $update = $conn->prepare("UPDATE solicitudes_BC SET estado = 'aprobado' WHERE id_solicitud = ?");
            $update->bind_param("i", $id);
            $update->execute();
        }
    } elseif ($accion === 'rechazar') {
        $update = $conn->prepare("UPDATE solicitudes_BC SET estado = 'rechazado' WHERE id_solicitud = ?");
        $update->bind_param("i", $id);
        $update->execute();
    }
}

// Obtener solicitudes
$stmt = $conn->prepare("SELECT s.*, t.nombre AS nombre_tecnico FROM solicitudes_nuevo_servicio s JOIN tecnicos t ON s.id_tecnico = t.id_tecnico WHERE s.estado = 'pendiente'");
$stmt->execute();
$result = $stmt->get_result();

$recursos_stmt = $conn->prepare("SELECT sr.*, t.nombre AS nombre_tecnico, cr.nombre_recurso, cr.descripcion FROM solicitudes_recursos sr JOIN tecnicos t ON sr.id_tecnico = t.id_tecnico JOIN catalogo_recursos cr ON sr.id_recurso = cr.id_recurso WHERE sr.estado = 'pendiente'");
$recursos_stmt->execute();
$recursos_result = $recursos_stmt->get_result();

$bc_stmt = $conn->prepare("SELECT sb.*, t.nombre AS nombre_tecnico FROM solicitudes_BC sb JOIN tecnicos t ON sb.id_tecnico = t.id_tecnico WHERE sb.estado = 'pendiente'");
$bc_stmt->execute();
$bc_result = $bc_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes Pendientes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="Admin_Empleados.php">Empleados</a>
        <a href="Admin_Encargados.php">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="gestionar_incidentes.php">Incidentes</a>
        <a href="soli_pend.php" class="active">Solicitudes Pendientes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Solicitudes de Nuevos Servicios</h1>
    <div class="table-container">
        <!-- tabla servicios -->
        <?php if ($result->num_rows > 0): ?>
        <table class="reportes-table" border="1">
            <thead>
                <tr>
                    <th>ID</th><th>Técnico</th><th>Servicio</th><th>Descripción</th><th>Costo</th><th>Tiempo</th><th>Fecha</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_solicitud'] ?></td>
                    <td><?= htmlspecialchars($row['nombre_tecnico']) ?></td>
                    <td><?= htmlspecialchars($row['nombre_servicio']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td>$<?= number_format($row['costo_sugerido'], 2) ?></td>
                    <td><?= $row['tiempo_estimado_minutos'] ?> min</td>
                    <td><?= $row['fecha_solicitud'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_solicitud" value="<?= $row['id_solicitud'] ?>">
                            <button name="accion" value="aceptar" class="btn">Aceptar</button>
                            <button name="accion" value="rechazar" class="btn" style="background-color:#cc0000;">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?><p>No hay solicitudes de servicios.</p><?php endif; ?>
    </div>

    <h1 class="page-title">Solicitudes de Recursos</h1>
    <div class="table-container">
        <!-- tabla recursos -->
        <?php if ($recursos_result->num_rows > 0): ?>
        <table class="reportes-table" border="1">
            <thead>
                <tr>
                    <th>ID</th><th>Técnico</th><th>Recurso</th><th>Descripción</th><th>Cantidad</th><th>Fecha</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($r = $recursos_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['id_solicitud'] ?></td>
                    <td><?= htmlspecialchars($r['nombre_tecnico']) ?></td>
                    <td><?= htmlspecialchars($r['nombre_recurso']) ?></td>
                    <td><?= htmlspecialchars($r['descripcion']) ?></td>
                    <td><?= $r['cantidad_solicitada'] ?></td>
                    <td><?= $r['fecha_solicitud'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_solicitud" value="<?= $r['id_solicitud'] ?>">
                            <button name="accion_recurso" value="aceptar" class="btn">Aceptar</button>
                            <button name="accion_recurso" value="rechazar" class="btn" style="background-color:#cc0000;">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?><p>No hay solicitudes de recursos.</p><?php endif; ?>
    </div>

    <h1 class="page-title">Solicitudes de Base de Conocimiento</h1>
    <div class="table-container">
        <!-- tabla BC -->
        <?php if ($bc_result->num_rows > 0): ?>
        <table class="reportes-table" border="1">
            <thead>
                <tr>
                    <th>ID</th><th>Técnico</th><th>Error</th><th>Solución</th><th>Fecha</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $bc_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_solicitud'] ?></td>
                    <td><?= htmlspecialchars($row['nombre_tecnico']) ?></td>
                    <td><?= htmlspecialchars($row['error']) ?></td>
                    <td><?= htmlspecialchars($row['solucion']) ?></td>
                    <td><?= $row['fecha_solicitud'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_solicitud_bc" value="<?= $row['id_solicitud'] ?>">
                            <button name="accion_bc" value="aceptar" class="btn">Aceptar</button>
                            <button name="accion_bc" value="rechazar" class="btn" style="background-color:#cc0000;">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?><p>No hay solicitudes de base de conocimiento.</p><?php endif; ?>
    </div>

    <a href="admin_dashboard.php" class="btn">Volver al Dashboard</a>
</main>
</body>
</html>

