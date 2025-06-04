<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Obtener término de búsqueda por ID de CI
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '' && is_numeric($busqueda)) {
    $stmt = $conn->prepare("SELECT * FROM ci_items WHERE id_ci = ?");
    $stmt->bind_param("i", $busqueda);
} else {
    $stmt = $conn->prepare("SELECT * FROM ci_items");
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Equipo</title>
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
        <a href="Admin_Equipo.php" class="active">Equipo</a>
        <a href="soli_pend.php">Solicitudes pendientes</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    
        <h1 class="page-title">Lista de Equipos Registrados</h1>

        <form method="GET" action="Admin_Equipo.php" class="search-form">
            <input 
                type="text" 
                name="buscar" 
                placeholder="Buscar por ID de CI" 
                value="<?= htmlspecialchars($busqueda) ?>" 
                class="input-search"
                autocomplete="off"
            />
            <button class="btn">Buscar</button>
            <a href="Reg_Equipo.php" class="btn">Nuevo CI</a>
        </form>

            <table class="table-container" border="1" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Ubicación</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th>Procesador</th>
                        <th>RAM</th>
                        <th>Display</th>
                        <th>Fuente de Poder</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($ci = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $ci['id_ci'] ?></td>
                            <td><?= htmlspecialchars($ci['nombre']) ?></td>
                            <td><?= htmlspecialchars($ci['tipo']) ?></td>
                            <td><?= htmlspecialchars($ci['descripcion']) ?></td>
                            <td><?= htmlspecialchars($ci['ubicacion']) ?></td>
                            <td><?= htmlspecialchars($ci['responsable']) ?></td>
                            <td><?= htmlspecialchars($ci['estado']) ?></td>
                            <td><?= htmlspecialchars($ci['procesador']) ?></td>
                            <td><?= htmlspecialchars($ci['ram']) ?></td>
                            <td><?= htmlspecialchars($ci['display']) ?></td>
                            <td><?= htmlspecialchars($ci['power_source']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

        <br>
        <a href="admin_dashboard.php" class="btn">Volver al Dashboard</a>
   
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
