<?php
session_start();
include 'db.php';

$conn = openConnection();

// Verificar si el usuario es administrador
if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Verificar que se haya pasado un ID por GET
if (!isset($_GET['id'])) {
    echo "ID no proporcionado.";
    exit();
}

$id = intval($_GET['id']);  // Seguridad: aseguramos entero

// Obtener datos actuales del encargado
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo = 'encargado'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$encargado = $result->fetch_assoc();

if (!$encargado) {
    echo "Encargado no encontrado.";
    exit();
}

// Procesar la modificación si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $sucursal = $_POST['sucursal'];

    $update = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, sucursal = ? WHERE id = ?");
    $update->bind_param("sssi", $nombre, $correo, $sucursal, $id);

    if ($update->execute()) {
        echo "<script>alert('Encargado actualizado correctamente'); window.location.href = 'admin_encargados.php';</script>";
        exit();
    } else {
        echo "<p class='error-message'>Error al actualizar: " . htmlspecialchars($conn->error) . "</p>";
    }

    $update->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Modificar Encargado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="admin_empleados.php">Empleados</a>
        <a href="admin_encargados.php" class="active">Encargados</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="dashboard-main">
    <div class="contenedor-principal">
        <h2 class="page-title">Modificar Encargado</h2>

        <form method="POST" class="formulario-editar">
            <label for="nombre">Nombre:</label><br>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($encargado['nombre']) ?>" 
                required 
                class="input-text"
            /><br><br>

            <label for="correo">Correo:</label><br>
            <input 
                type="email" 
                id="correo" 
                name="correo" 
                value="<?= htmlspecialchars($encargado['correo']) ?>" 
                required 
                class="input-text"
            /><br><br>

            <label for="sucursal">Sucursal:</label><br>
            <select id="sucursal" name="sucursal" required class="input-text">
                <option value="Sucursal A" <?= $encargado['sucursal'] === 'Sucursal A' ? 'selected' : '' ?>>Sucursal A</option>
                <option value="Sucursal B" <?= $encargado['sucursal'] === 'Sucursal B' ? 'selected' : '' ?>>Sucursal B</option>
            </select><br><br>

            <button type="submit" class="btn btn-yellow">Guardar Cambios</button>
        </form>

        <br>
        <a href="admin_encargados.php" class="btn btn-back">Volver</a>
    </div>
</main>

</body>
</html>
