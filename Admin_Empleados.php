<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '') {
    $stmt = $conn->prepare("SELECT * FROM tecnicos WHERE nombre LIKE ?");
    $param = "%" . $busqueda . "%";
    $stmt->bind_param("s", $param);
} else {
    $stmt = $conn->prepare("SELECT * FROM tecnicos");
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Técnicos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="Admin_Empleados.php" class="active">Empleados</a>
        <a href="Admin_Encargados.php">Encargados</a>
          <a href="Admin_Equipo.php">Equipo</a>
          <a href="soli_pend.php">Solicitudes pendientes</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
        <h1 class="page-title">Lista de Técnicos</h1>
        <form method="GET" action="Admin_Empleados.php" class="search-form">
            <input 
                type="text" 
                name="buscar" 
                placeholder="Buscar por nombre" 
                value="<?= htmlspecialchars($busqueda) ?>" 
                class="input-search"
                autocomplete="off"
            />
            <button class="btn" >Buscar</button>
            <a href="Reg_Tecnico.php" class="btn">Técnico nuevo</a>
        </form>

        <table class="table-container">
            <thead>
                <tr>
                    <th>ID Técnico</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Disponibilidad</th>
                    <th>RFC</th>
                    <th>Desempeño</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($tecnico = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $tecnico['id_tecnico'] ?></td>
                    <td><?= htmlspecialchars($tecnico['nombre']) ?></td>
                    <td><?= htmlspecialchars($tecnico['especialidad']) ?></td>
                    <td><?= htmlspecialchars($tecnico['disponibilidad']) ?></td>
                    <td><?= htmlspecialchars($tecnico['rfc']) ?></td>
                    <td><?= number_format($tecnico['desempeno'], 2) ?> / 5</td>
                    <td><a href="Mod_Empleado.php?id=<?= $tecnico['id_tecnico'] ?>" class="link-action">Modificar</a></td>
                </tr>
            <?php endwhile; ?>
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
