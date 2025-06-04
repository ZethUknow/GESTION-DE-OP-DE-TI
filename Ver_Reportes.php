<?php
session_start();
include 'db.php';

// Verificar que el usuario sea un encargado
if ($_SESSION['tipo_usuario'] != 'encargado') {
    header("Location: login.php");
    exit();
}

$encargado_nombre = $_SESSION['nombre_usuario']; // Nombre del encargado logueado

$conn = openConnection();

// Modificar la consulta para que busque los incidentes por el nombre del encargado
$query = "SELECT * FROM incidentes WHERE responsable = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $encargado_nombre); // Usar el nombre como parámetro
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reportes</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="encargado_dashboard.php">Inicio</a>
         <a href="reportar_incidente.php">Reportar Incidente</a>
        <a href="Ver_Reportes.php" class="active">Mis Reportes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Mis Reportes</h1>
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
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): 
                    $estado = strtolower(trim($row['estado']));
                ?>
                    <tr>
                        <td><?= $row['id_incidente'] ?></td>
                        <td><?= htmlspecialchars($row['titulo']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion']) ?></td>
                        <td><?= htmlspecialchars($row['estado']) ?></td>
                        <td><?= htmlspecialchars($row['prioridad']) ?></td>
                        <td><?= $row['fecha_reporte'] ?></td>
                        <td>
                            <?php if ($estado === 'terminado'): ?>
                                <a href="form_evaluar_incidente.php?id=<?= $row['id_incidente'] ?>" class="btn">Evaluar</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="7">No se encontraron reportes.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>