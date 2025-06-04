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
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $sucursal = $_POST['sucursal'];

    $sucursales_validas = ['Sucursal A', 'Sucursal B'];

    if ($nombre && $correo && $password && $confirm_password && in_array($sucursal, $sucursales_validas)) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = "Correo inválido.";
        } elseif ($password !== $confirm_password) {
            $error = "Las contraseñas no coinciden.";
        } else {
            $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt_check->bind_param("s", $correo);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error = "El correo ya está registrado.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, password, tipo, sucursal) VALUES (?, ?, ?, 'encargado', ?)");
                $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $sucursal);

                if ($stmt->execute()) {
                    $stmt->close();
                    $stmt_check->close();
                    $conn->close();
                    header("Location: admin_encargados.php");
                    exit();
                } else {
                    $error = "Error al registrar encargado.";
                }
                $stmt->close();
            }
            $stmt_check->close();
        }
    } else {
        $error = "Todos los campos son obligatorios y la sucursal debe ser válida.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrar Encargado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="admin_empleados.php">Empleados</a>
        <a href="admin_encargados.php" class="active">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="gestionar_incidentes.php">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <div class="contenedor-principal">
        <h1 class="page-title">Registrar Nuevo Encargado</h1>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="Reg_Encargado.php" class="formulario-editar">
            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre" required class="input-text"><br><br>

            <label for="correo">Correo:</label><br>
            <input type="email" id="correo" name="correo" required class="input-text"><br><br>

            <label for="password">Contraseña:</label><br>
            <input type="password" id="password" name="password" required class="input-text"><br><br>

            <label for="confirm_password">Confirmar Contraseña:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required class="input-text"><br><br>

            <label for="sucursal">Sucursal:</label><br>
            <select id="sucursal" name="sucursal" required class="input-text">
                <option value="" disabled selected>Selecciona una sucursal</option>
                <option value="Sucursal A">Sucursal A</option>
                <option value="Sucursal B">Sucursal B</option>
            </select>
            <br><br>

            <button type="submit" class="btn btn-yellow">Registrar Encargado</button>
        </form>

        <br>
        <a href="admin_encargados.php" class="btn btn-back">Volver a Lista de Encargados</a>
    </div>
</main>

</body>
</html>
