<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

$estados_validos = ['enviado', 'en proceso', 'terminado', 'evaluado', 'rechazada'];

$consulta_base = "
    SELECT i.*, 
           t.nombre AS tecnico_nombre, 
           ci.nombre AS ci_nombre, ci.id_ci, ci.responsable AS ci_responsable, ci.procesador, ci.ram, ci.display,
           u.nombre AS encargado_nombre, u.sucursal AS sucursal_nombre 
    FROM incidentes i 
    LEFT JOIN tecnicos t ON i.id_tecnico_asignado = t.id_tecnico
    LEFT JOIN ci_items ci ON i.id_ci_afectado = ci.id_ci
    LEFT JOIN usuarios u ON i.responsable = u.nombre
";

if ($busqueda !== '') {
    $consulta_base .= " WHERE i.id_incidente = ?";
    $stmt = $conn->prepare($consulta_base);
    $stmt->bind_param("i", $busqueda);
} elseif (in_array($estado, $estados_validos)) {
    $consulta_base .= " WHERE i.estado = ?";
    $stmt = $conn->prepare($consulta_base);
    $stmt->bind_param("s", $estado);
} else {
    $stmt = $conn->prepare($consulta_base);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- HTML a partir de aquí -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestionar Incidentes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="Admin_Empleados.php">Empleados</a>
        <a href="Admin_Encargados.php">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="soli_pend.php">Solicitudes pendientes</a>
        <a href="gestionar_incidentes.php" class="active">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Gestionar Incidentes</h1>

    <form method="GET" action="gestionar_incidentes.php" class="input-personalizado">
        <input 
            type="number" 
            name="buscar" 
            placeholder="Buscar por ID de Incidente" 
            value="<?= htmlspecialchars($busqueda) ?>" 
            class="input-personalizado"
        />
        <select name="estado">
            <option value="">-- Filtrar por estado --</option>
            <?php foreach ($estados_validos as $estado_opcion): ?>
                <option value="<?= $estado_opcion ?>" <?= $estado_opcion === $estado ? 'selected' : '' ?>>
                    <?= ucfirst($estado_opcion) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Buscar</button>
    </form>

    <div class="table-container">
        <table class="reportes-table" border="1">
            <thead>
                <tr>
                    <th>ID</th><th>Título</th><th>Descripción</th><th>Estado</th><th>Prioridad</th>
                    <th>Técnico Asignado</th><th>CI Afectado</th><th>ID CI</th><th>Responsable CI</th>
                    <th>Procesador</th><th>RAM</th><th>Display</th>
                    <th>Encargado (Sucursal)</th><th>Fecha Límite</th><th>Resolución</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_incidente'] ?></td>
                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                    <td><?= htmlspecialchars($row['prioridad']) ?></td>
                    <td><?= $row['tecnico_nombre'] ?? 'No asignado' ?></td>
                    <td><?= $row['ci_nombre'] ?? 'No asignado' ?></td>
                    <td><?= $row['id_ci'] ?? 'N/A' ?></td>
                    <td><?= $row['ci_responsable'] ?? 'N/A' ?></td>
                    <td><?= $row['procesador'] ?? 'N/A' ?></td>
                    <td><?= $row['ram'] ?? 'N/A' ?></td>
                    <td><?= $row['display'] ?? 'N/A' ?></td>
                    <td><?= $row['encargado_nombre'] ? $row['encargado_nombre'] . ' (' . $row['sucursal_nombre'] . ')' : 'No asignado' ?></td>
                    <td><?= $row['fecha_limite'] ?? 'No asignada' ?></td>
                    <td><?= $row['resolucion'] ?? 'Sin resolución' ?></td>
                    <td>
                        <a href="Res_Incidente.php?id=<?= urlencode($row['id_incidente']) ?>" class="link-action">Gestionar</a> |
                        <a href="imprimir.php?id=<?= urlencode($row['id_incidente']) ?>" target="_blank" class="link-action">Imprimir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="admin_dashboard.php" class="btn">Volver al Dashboard</a>
</main>
</body>
</html>

<?php
$conn->close();
?>
