<?php
session_start();
include 'db.php';

$conn = openConnection();

// Verificar sesión de admin
if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Validar que se reciba el ID del técnico
if (!isset($_GET['id'])) {
    echo "ID del técnico no especificado.";
    exit();
}

$id = intval($_GET['id']);

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $especialidad = trim($_POST['especialidad']);
    $rfc = trim($_POST['rfc']);

    $stmt = $conn->prepare("UPDATE tecnicos SET nombre = ?, especialidad = ?, rfc = ? WHERE id_tecnico = ?");
    $stmt->bind_param("sssi", $nombre, $especialidad, $rfc, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Técnico actualizado correctamente'); window.location.href = 'admin_empleados.php';</script>";
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener datos actuales del técnico
$stmt = $conn->prepare("SELECT * FROM tecnicos WHERE id_tecnico = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Técnico no encontrado.";
    exit();
}

$tecnico = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Modificar Técnico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="Admin_Empleados.php" class="active">Empleados</a>
        <a href="Admin_Encargados.php">Encargados</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="dashboard-main">
    <div class="contenedor-principal">
        <h1 class="page-title">Modificar Técnico</h1>

        <form method="POST" class="formulario-editar">
            <label for="nombre">Nombre:</label><br>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($tecnico['nombre']) ?>" 
                required
                class="input-text"
            /><br><br>

            <label for="especialidad">Especialidad:</label><br>
            <input 
                type="text" 
                id="especialidad" 
                name="especialidad" 
                value="<?= htmlspecialchars($tecnico['especialidad']) ?>" 
                required
                class="input-text"
            /><br><br>

            <label for="rfc">RFC:</label><br>
            <input 
                type="text" 
                id="rfc" 
                name="rfc" 
                value="<?= htmlspecialchars($tecnico['rfc']) ?>" 
                maxlength="13" 
                required
                class="input-text"
            /><br><br>

            <button type="submit" class="btn btn-yellow">Guardar cambios</button>
        </form>

        <br>
        <a href="admin_empleados.php" class="btn btn-back">Volver</a>
    </div>
</main>

</body>
</html>

<?php
$conn->close();
?>
