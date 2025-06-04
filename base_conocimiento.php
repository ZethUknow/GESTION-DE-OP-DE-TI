<?php
session_start();
include 'db.php';
$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['tecnico', 'admin'])) {
    header("Location: login.php");
    exit();
}

$buscar_id = isset($_GET['id_entry']) ? intval($_GET['id_entry']) : 0;

if ($buscar_id > 0) {
    $stmt = $conn->prepare("
        SELECT bc.*, t.nombre AS tecnico_nombre 
        FROM Base_De_Conocimiento bc 
        JOIN tecnicos t ON bc.id_tecnico = t.id_tecnico
        WHERE bc.id_entry = ?
        ORDER BY fecha_registro DESC
    ");
    $stmt->bind_param("i", $buscar_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "
        SELECT bc.*, t.nombre AS tecnico_nombre 
        FROM Base_De_Conocimiento bc 
        JOIN tecnicos t ON bc.id_tecnico = t.id_tecnico
        ORDER BY fecha_registro DESC
    ";
    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Base de Conocimiento</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <?php if ($_SESSION['tipo_usuario'] === 'tecnico'): ?>
            <a href="tecnico_dashboard.php">Inicio</a>
            <a href="base_conocimiento.php" class="active">Base de conocimiento</a>
            <a href="ver_reportes_asignados.php">Ver Reportes Asignados</a>
        <?php elseif ($_SESSION['tipo_usuario'] === 'admin'): ?>
            <a href="admin_dashboard.php">Inicio</a>
            <a href="gestionar_incidentes.php">Incidentes</a>
            <a href="soli_pend.php">Solicitudes</a>
            <a href="base_conocimiento.php" class="active">Base de conocimiento</a>
        <?php endif; ?>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Base de Conocimiento</h1>

    <form method="GET" action="base_conocimiento.php" class="search-form">
        <input 
            type="number" 
            name="id_entry" 
            placeholder="Buscar por ID de entrada" 
            value="<?= $buscar_id > 0 ? $buscar_id : '' ?>" 
            class="input-search"
        />
        <button class="btn">Buscar</button>
        <a href="base_conocimiento.php" class="btn">Limpiar</a>
    </form>

    <div class="table-container">
        <table class="reportes-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Error</th>
                    <th>Solución</th>
                    <th>Técnico</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_entry'] ?></td>
                            <td><?= htmlspecialchars($row['error']) ?></td>
                            <td><?= htmlspecialchars($row['solucion']) ?></td>
                            <td><?= htmlspecialchars($row['tecnico_nombre']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No se encontraron resultados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>

<?php $conn->close(); ?>
