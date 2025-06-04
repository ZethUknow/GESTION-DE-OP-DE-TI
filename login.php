<?php
session_start();
include 'db.php';

$conn = openConnection();
$mensaje_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificador = $_POST['identificador'];
    $clave = $_POST['clave'];

    // Primero intentamos con usuarios (correo y password)
    $sql_usuario = "SELECT id, nombre, correo, password, tipo, sucursal FROM usuarios WHERE correo = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("s", $identificador);
    $stmt_usuario->execute();
    $resultado_usuario = $stmt_usuario->get_result();

    if ($resultado_usuario->num_rows > 0) {
        $usuario = $resultado_usuario->fetch_assoc();
        if (password_verify($clave, $usuario['password'])) {
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['tipo_usuario'] = $usuario['tipo'];
            $_SESSION['nombre_usuario'] = $usuario['nombre'];
            $_SESSION['sucursal'] = $usuario['sucursal'];

            if ($usuario['tipo'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: encargado_dashboard.php");
            }
            exit();
        } else {
            $mensaje_error = 'Contraseña incorrecta.';
        }
    } else {
        // Si no es usuario, intentamos como técnico
        $sql_tecnico = "SELECT id_tecnico, nombre, especialidad FROM tecnicos WHERE nombre = ? AND rfc = ?";
        $stmt_tecnico = $conn->prepare($sql_tecnico);
        $stmt_tecnico->bind_param("ss", $identificador, $clave);
        $stmt_tecnico->execute();
        $resultado_tecnico = $stmt_tecnico->get_result();

        if ($resultado_tecnico->num_rows > 0) {
            $tecnico = $resultado_tecnico->fetch_assoc();
            $_SESSION['id_tecnico'] = $tecnico['id_tecnico'];
            $_SESSION['tipo_usuario'] = 'tecnico';
            $_SESSION['nombre_usuario'] = $tecnico['nombre'];
            $_SESSION['especialidad'] = $tecnico['especialidad'];

            header("Location: tecnico_dashboard.php");
            exit();
        } else {
            $mensaje_error = 'Credenciales incorrectas.';
        }

        $stmt_tecnico->close();
    }

    $stmt_usuario->close();
    closeConnection($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - SmartFit</title>
    <link rel="stylesheet" href="style/styles.css" />
</head>
<body>

<div class="login-container">
    <h2>Iniciar sesión</h2>

    <?php if ($mensaje_error !== ''): ?>
        <div class="mensaje-error"><?= htmlspecialchars($mensaje_error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="identificador" placeholder="Correo (usuarios) o Nombre (técnicos)" required />
        <input type="password" name="clave" placeholder="Contraseña (usuarios) o RFC (técnicos)" required />
        <button class="btn">Iniciar Sesión</button>
    </form>
</div>

</body>
</html>
