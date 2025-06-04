<?php
session_start();
include 'db.php';

$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'tecnico') {
    header("Location: login.php");
    exit();
}

$id_tecnico = $_GET['id_tecnico'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_servicio = $_POST['nombre_servicio'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $costo_sugerido = $_POST['costo_sugerido'] ?? 0;
    $tiempo_estimado = $_POST['tiempo_estimado_minutos'] ?? 0;

    if ($nombre_servicio && $descripcion && $costo_sugerido > 0 && $tiempo_estimado > 0) {
        $stmt = $conn->prepare("INSERT INTO solicitudes_nuevo_servicio 
            (id_tecnico, nombre_servicio, descripcion, costo_sugerido, tiempo_estimado_minutos)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdi", $id_tecnico, $nombre_servicio, $descripcion, $costo_sugerido, $tiempo_estimado);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Solicitud enviada correctamente.</p>";
        } else {
            echo "<p style='color: red;'>Error al enviar la solicitud.</p>";
        }
    } else {
        echo "<p style='color: red;'>Por favor, completa todos los campos correctamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Nuevo Servicio</title>
    <link rel="stylesheet" href="style/styles.css"> <!-- Ajusta esta ruta a tu CSS -->
</head>
<body>
<div class="form-container">
    <h2>Solicitar un Nuevo Servicio</h2>
    <form method="POST" action="">
        <input type="hidden" name="id_tecnico" value="<?= htmlspecialchars($id_tecnico) ?>">

        <label for="nombre_servicio">Nombre del Servicio:</label><br>
        <input type="text" id="nombre_servicio" name="nombre_servicio" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="4" required></textarea><br><br>

        <label for="costo_sugerido">Costo Sugerido ($):</label><br>
        <input type="number" id="costo_sugerido" name="costo_sugerido" step="0.01" required><br><br>

        <label for="tiempo_estimado_minutos">Tiempo Estimado (minutos):</label><br>
        <input type="number" id="tiempo_estimado_minutos" name="tiempo_estimado_minutos" required><br><br>

        <button type="submit" class="btn">Enviar Solicitud</button>
        <a href="javascript:history.back()" class="btn" style="margin-left: 10px;">Cancelar</a>
    </form>
</div>
</body>
</html>
