<?php
session_start();
include 'db.php';

if ($_SESSION['tipo_usuario'] != 'tecnico') {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION['nombre_usuario'] ?? 'Técnico';
$especialidad = $_SESSION['especialidad'] ?? 'Sin especialidad';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Técnico</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <header class="dashboard-header">
        <nav class="dashboard-nav">
            <a href="tecnico_dashboard.php">Inicio</a>
            <a href="base_conocimiento.php">Base de conocimiento</a>
            <a href="ver_reportes_asignados.php">Ver Reportes Asignados</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main class="main-container">
        <h1 class="tecnico-nombre">Bienvenido, <?= htmlspecialchars($nombre) ?></h1>
        <p><strong>Especialidad:</strong> <?= htmlspecialchars($especialidad) ?></p>
    </main>
</body>
</html>
