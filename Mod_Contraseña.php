<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] !== 'encargado') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    echo "Usuario no identificado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contrasena_actual = $_POST['contrasena_actual'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

    // Obtener contraseña actual de la BD
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $error = "Usuario no encontrado.";
    } else {
        $user = $result->fetch_assoc();

        // Verificar contraseña actual
        if (!password_verify($contrasena_actual, $user['password'])) {
            $error = "La contraseña actual es incorrecta.";
        } else {
            // Encriptar nueva contraseña
            $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

            // Actualizar en la BD
            $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $nueva_contrasena_hash, $id_usuario);

            if ($stmt->execute()) {
                $mensaje = "Contraseña actualizada correctamente.";
            } else {
                $error = "Error al actualizar la contraseña.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="encargado_dashboard.php">Inicio</a>
        <a href="reportar_incidente.php">Reportar Incidente</a>
        <a href="Ver_Reportes.php">Ver mis reportes</a>
        <a href="Mod_Contraseña.php" class="active">Cambiar Contraseña</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Cambiar Contraseña</h1>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="formulario-cambio">
        <label for="contrasena_actual">Contraseña Actual:</label><br>
        <input type="password" name="contrasena_actual" id="contrasena_actual" required><br><br>

        <label for="nueva_contrasena">Nueva Contraseña:</label><br>
        <input type="password" name="nueva_contrasena" id="nueva_contrasena" required><br><br>

        <button class="btn">Actualizar Contraseña</button>
    </form>

    <br>
    <a href="encargado_dashboard.php" class="btn">Volver al Dashboard</a>
</main>

</body>
</html>
