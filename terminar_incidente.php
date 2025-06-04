<?php
session_start();
include 'db.php';

$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'tecnico') {
    header("Location: login.php");
    exit();
}

$id_tecnico = $_SESSION['id_tecnico'];
$id_incidente = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_incidente <= 0) {
    echo "Incidente no especificado.";
    exit();
}

// Verificar que el incidente pertenezca al técnico
$verificar = $conn->prepare("SELECT * FROM incidentes WHERE id_incidente = ? AND id_tecnico_asignado = ?");
$verificar->bind_param("ii", $id_incidente, $id_tecnico);
$verificar->execute();
$res = $verificar->get_result();

if ($res->num_rows === 0) {
    echo "No tienes acceso a este incidente.";
    exit();
}

$incidente = $res->fetch_assoc();
$error_bd = $incidente['titulo']; // Posible error técnico

$resolucion_actual = $incidente['resolucion'] ?? '';
$mensaje_bc = "";

// Si se presiona "Terminar Incidente"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === "terminar") {
    if (!empty($_POST['resolucion'])) {
        $resolucion = trim($_POST['resolucion']);

        $stmt = $conn->prepare("UPDATE incidentes SET resolucion = ?, estado = 'terminado', fecha_resolucion = NOW() WHERE id_incidente = ?");
        $stmt->bind_param("si", $resolucion, $id_incidente);

        if ($stmt->execute()) {
            header("Location: ver_reportes_asignados.php?msg=incidente_terminado");
            exit();
        } else {
            $error = "Error al guardar resolución.";
        }
    } else {
        $error = "Debe escribir una resolución para finalizar el incidente.";
    }
}

// Si se presiona "Solicitar BC"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === "solicitar_bc") {
    $resolucion = trim($_POST['resolucion'] ?? '');
    if ($resolucion !== '') {
        $error_texto = $error_bd;

        $check = $conn->prepare("SELECT id_solicitud FROM solicitudes_BC WHERE id_incidente = ?");
        $check->bind_param("i", $id_incidente);
        $check->execute();
        $res_check = $check->get_result();

        if ($res_check->num_rows > 0) {
            $mensaje_bc = "Ya existe una solicitud para este incidente.";
        } else {
            $insertBC = $conn->prepare("INSERT INTO solicitudes_BC (id_tecnico, id_incidente, error, solucion, estado, fecha_solicitud) VALUES (?, ?, ?, ?, 'pendiente', NOW())");
            $insertBC->bind_param("iiss", $id_tecnico, $id_incidente, $error_texto, $resolucion);

            if ($insertBC->execute()) {
                $mensaje_bc = "✅ Solicitud enviada a la base de conocimiento.";
            } else {
                $mensaje_bc = "❌ Error al enviar la solicitud.";
            }
        }
    } else {
        $mensaje_bc = "⚠️ Escriba una resolución antes de enviar la solicitud.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Terminar Incidente #<?= $id_incidente ?></title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="tecnico_dashboard.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="ver_reportes_asignados.php">Ver Reportes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<div class="main-container">
    <h2>Finalizar Incidente #<?= $id_incidente ?></h2>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (!empty($mensaje_bc)): ?>
        <p style="color:<?= strpos($mensaje_bc, '✅') !== false ? 'green' : 'orange' ?>;"><?= $mensaje_bc ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="resolucion">Escribe la resolución final del incidente:</label><br>
        <textarea name="resolucion" id="resolucion" rows="6" style="width:100%;" required><?= htmlspecialchars($_POST['resolucion'] ?? $resolucion_actual) ?></textarea><br><br>

        <button type="submit" name="accion" value="terminar" class="btn">Terminar Incidente</button>
        <button type="submit" name="accion" value="solicitar_bc" class="btn" style="background-color: #17a2b8;">Solicitar agregar a base de conocimiento</button>
        <a href="ver_reportes_asignados.php" class="btn">Cancelar</a>
    </form>
</div>
</body>
</html>
