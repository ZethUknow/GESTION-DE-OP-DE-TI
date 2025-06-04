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
    $especialidad = trim($_POST['especialidad']);
    $disponibilidad = $_POST['disponibilidad'] ?? 'Disponible';
    $rfc = strtoupper(trim($_POST['rfc']));

    if ($nombre && $especialidad && $rfc && strlen($rfc) === 13) {
        $stmt = $conn->prepare("INSERT INTO tecnicos (nombre, especialidad, disponibilidad, rfc) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $especialidad, $disponibilidad, $rfc);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: admin_empleados.php");
            exit();
        } else {
            $error = "Error al registrar técnico.";
        }

        $stmt->close();
    } else {
        $error = "Todos los campos son obligatorios y el RFC debe tener 13 caracteres.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrar Técnico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="Admin_Empleados.php" class="active">Empleados</a>
        <a href="Admin_Encargados.php">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <div class="contenedor-principal">
        <h1 class="page-title">Registrar Nuevo Técnico</h1>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="formulario-editar">
            <label for="nombre">Nombre:</label><br>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                required 
                class="input-text"
            /><br><br>

            <label for="especialidad">Especialidad:</label><br>
            <input 
                type="text" 
                id="especialidad" 
                name="especialidad" 
                required 
                class="input-text"
            /><br><br>

            <label for="disponibilidad">Disponibilidad:</label><br>
            <select id="disponibilidad" name="disponibilidad" class="input-text">
                <option value="Disponible">Disponible</option>
                <option value="En servicio">En servicio</option>
            </select><br><br>

            <label for="rfc">RFC (13 caracteres):</label><br>
            <input 
                type="text" 
                id="rfc" 
                name="rfc" 
                maxlength="13" 
                required 
                class="input-text"
            /><br><br>

            <button type="submit" class="btn btn-yellow">Registrar Técnico</button>
        </form>

        <br>
        <a href="admin_empleados.php" class="btn btn-back">Volver a Lista de Técnicos</a>
    </div>
</main>

</body>
</html>
