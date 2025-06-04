<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Obtener término de búsqueda si existe
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '') {
    $stmt = $conn->prepare("SELECT id, nombre, correo, sucursal FROM usuarios WHERE tipo = 'encargado' AND nombre LIKE ?");
    $param = "%" . $busqueda . "%";
    $stmt->bind_param("s", $param);
} else {
    $stmt = $conn->prepare("SELECT id, nombre, correo, sucursal FROM usuarios WHERE tipo = 'encargado'");
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Encargados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="Admin_Empleados.php">Empleados</a>
        <a href="admin_encargados.php" class="active">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="soli_pend.php">Solicitudes pendientes</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    
        <h1 class="page-title">Lista de Encargados</h1>

        <form method="GET" action="admin_encargados.php" class="search-form">
            <input 
                type="text" 
                name="buscar" 
                placeholder="Buscar por nombre" 
                value="<?= htmlspecialchars($busqueda) ?>" 
                class="input-search"
                autocomplete="off"
            />
            <button class="btn">Buscar</button>
            <a href="Reg_Encargado.php" class="btn">Encargado nuevo</a>
        </form>

        <div class="table-container">
            <table class="reportes-table" border="1" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Sucursal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['correo']) ?></td>
                        <td><?= htmlspecialchars($row['sucursal']) ?></td>
                        <td><a href="Mod_Encargado.php?id=<?= $row['id'] ?>" class="link-action">Modificar</a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <br>
        <a href="admin_dashboard.php" class="btn">Volver al Dashboard</a>
        
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
