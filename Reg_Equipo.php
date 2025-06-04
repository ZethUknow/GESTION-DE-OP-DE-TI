<?php
session_start();
include 'db.php';

if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$conn = openConnection();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $tipo = trim($_POST['tipo']);
    $descripcion = trim($_POST['descripcion']);
    $ubicacion = trim($_POST['ubicacion']);
    $responsable = trim($_POST['responsable']);
    $estado = trim($_POST['estado']);
    $procesador = $_POST['procesador'] ?: 'Sin Componente';
    $ram = $_POST['ram'] ?: 'Sin Componente';
    $display = $_POST['display'] ?: 'Sin Componente';
    $power_source = $_POST['power_source'] ?: 'Sin Componente';

    if ($nombre && $tipo && $ubicacion && $responsable && $estado) {
        $stmt = $conn->prepare("INSERT INTO ci_items 
            (nombre, tipo, descripcion, ubicacion, responsable, estado, procesador, ram, display, power_source)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nombre, $tipo, $descripcion, $ubicacion, $responsable, $estado, $procesador, $ram, $display, $power_source);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: admin_equipos.php");
            exit();
        } else {
            $error = "Error al registrar el equipo.";
        }
        $stmt->close();
    } else {
        $error = "Todos los campos obligatorios deben ser completados.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Equipo</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="admin_empleados.php">Empleados</a>
        <a href="admin_encargados.php">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Registrar Nuevo Equipo</h1>
    <div class="main-container">
        

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="Reg_Equipo.php" class="formulario-editar">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required class="input-text"><br>

            <label for="tipo">Tipo:</label>
            <input type="text" name="tipo" id="tipo" required class="input-text"><br>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="input-text"></textarea><br>

            <label for="ubicacion">Ubicación:</label>
            <input type="text" name="ubicacion" id="ubicacion" required class="input-text"><br>

            <label for="responsable">Responsable:</label>
            <input type="text" name="responsable" id="responsable" required class="input-text"><br>

            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required class="input-text">
                <option value="" disabled selected>Selecciona estado</option>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
                <option value="En Reparación">En Reparación</option>
            </select><br>

            <label for="procesador">Procesador:</label>
            <input type="text" name="procesador" id="procesador" class="input-text"><br>

            <label for="ram">RAM:</label>
            <input type="text" name="ram" id="ram" class="input-text"><br>

            <label for="display">Display:</label>
            <input type="text" name="display" id="display" class="input-text"><br>

            <label for="power_source">Fuente de Alimentación:</label>
            <input type="text" name="power_source" id="power_source" class="input-text"><br>

            <button type="submit" class="btn btn-yellow">Registrar Equipo</button>
        </form>

        <br>
        <a href="admin_equipo.php" class="btn btn-back">Volver a Lista de Equipos</a>
    </div>
</main>

</body>
</html>
