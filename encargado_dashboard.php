<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'encargado') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'] ?? null;

$mensaje_error = '';

if (!$id_usuario) {
    $mensaje_error = "Error: usuario no identificado.";
} else {
    $stmt = $conn->prepare("SELECT nombre, correo, tipo, sucursal FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $mensaje_error = "Usuario no encontrado.";
    } else {
        $encargado = $result->fetch_assoc();
    }
    $stmt->close();
}

closeConnection($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Encargado</title>
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<?php if ($mensaje_error !== ''): ?>
    <div class="mensaje-error"><?= htmlspecialchars($mensaje_error) ?></div>
<?php else: ?>

    <header class="dashboard-header">
        <nav class="dashboard-nav">
            <a href="encargado_dashboard.php">Inicio</a>
            <a href="reportar_incidente.php">Reportar Incidente</a>
            <a href="Ver_Reportes.php">Ver mis reportes</a>
            <a href="Mod_Contraseña.php">Cambiar Contraseña</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main class="main-container">
        <h1 class="encargado-nombre">Bienvenido,<?= htmlspecialchars($encargado['nombre']) ?></h1>
        <p><strong>Correo:</strong> <?= htmlspecialchars($encargado['correo']) ?></p>
        <p><strong>Tipo de usuario:</strong> <?= htmlspecialchars($encargado['tipo']) ?></p>
        <p><strong>Sucursal:</strong> <?= htmlspecialchars($encargado['sucursal']) ?></p>
    </main>

<?php endif; ?>

</body>
</html>
