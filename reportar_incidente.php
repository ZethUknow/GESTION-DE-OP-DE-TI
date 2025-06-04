<?php
session_start();
include 'db.php';

$conn = openConnection();

// Verificar si el usuario es un encargado
if ($_SESSION['tipo_usuario'] != 'encargado') {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$mensaje_tipo = ''; // 'exito' o 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $id_ci = $_POST['id_ci'];
    $responsable = $_SESSION['nombre_usuario'];  // Nombre del encargado desde la sesión

    // Insertar incidente
    $query = "INSERT INTO incidentes (titulo, descripcion, id_ci_afectado, responsable) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssis", $titulo, $descripcion, $id_ci, $responsable);

    if ($stmt->execute()) {
        $mensaje = "Incidente reportado correctamente";
        $mensaje_tipo = 'exito';
    } else {
        $mensaje = "Error al reportar incidente.";
        $mensaje_tipo = 'error';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reportar Incidente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="encargado_dashboard.php">Inicio</a>
        <a href="reportar_incidente.php" class="active">Reportar Incidente</a>
        <a href="Ver_Reportes.php">Ver mis reportes</a>
        <a href="Mod_Contraseña.php">Cambiar Contraseña</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">

    <h1 class="page-title">Reportar Nuevo Incidente</h1>

    <?php if ($mensaje): ?>
        <div class="mensaje-<?= $mensaje_tipo ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="form-reportar-incidente" autocomplete="off">

        <label for="titulo">Título del incidente</label>
        <input type="text" id="titulo" name="titulo" placeholder="Título del incidente" required>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" placeholder="Descripción detallada" required></textarea>

        <label for="id_ci">Equipo o CI afectado</label>
        <select name="id_ci" id="id_ci" required>
            <?php
            $result = $conn->query("SELECT id_ci, nombre FROM ci_items WHERE ubicacion = '{$_SESSION['sucursal']}'");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id_ci']}'>" . htmlspecialchars($row['nombre']) . "</option>";
            }
            ?>
        </select>

        <button class="btn">Reportar</button>
        
    </form>
 <a href="encargado_dashboard.php" class="btn btn-back">Volver al Dashboard</a>
</main>

</body>
</html>
