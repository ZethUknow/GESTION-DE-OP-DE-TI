<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    echo "Error: usuario no identificado.";
    exit();
}

$stmt = $conn->prepare("SELECT nombre, correo, tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Usuario no encontrado.";
    exit();
}

$admin = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php" class="active">Inicio</a>
        <a href="base_conocimiento.php" class="active">Base de conocimiento</a>
        <a href="Admin_Empleados.php">Empleados</a>
        <a href="Admin_Encargados.php">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="soli_pend.php">Solicitudes pendientes</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Bienvenido, <?= htmlspecialchars($admin['nombre']) ?></h1>

    <div class="info-box">
        <p><strong>Correo:</strong> <?= htmlspecialchars($admin['correo']) ?></p>
        <p><strong>Tipo de usuario:</strong> <?= htmlspecialchars($admin['tipo']) ?></p>
    </div>

    <hr>

    <!-- Aquí puedes agregar contenido dinámico o funcionalidad extra -->
</main>

</body>
</html>
