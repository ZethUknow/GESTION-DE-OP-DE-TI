<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $sucursal = $_POST['sucursal'];

    // Si el tipo de usuario es administrador, asignar sucursal a NULL
    if ($tipo_usuario == 'admin') {
        $sucursal = NULL;
    }

    // Validación básica
    if (empty($nombre) || empty($correo) || empty($password) || empty($tipo_usuario)) {
        echo "<script>alert('Por favor, completa todos los campos.');</script>";
    } else {
        // Encriptar la contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Abrir conexión a la base de datos
        $conn = openConnection();

        // Consulta para insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, correo, password, tipo, sucursal) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $correo, $password_hash, $tipo_usuario, $sucursal);

        if ($stmt->execute()) {
            // Redirigir al login después de un registro exitoso
            header("Location: login.php");
            exit(); // Asegurarse de que no se ejecute más código después de la redirección
        } else {
            echo "<script>alert('Error al registrar el usuario.');</script>";
        }

        // Cerrar conexión
        $stmt->close();
        closeConnection($conn);
    }
}
?>

<!-- Formulario de Registro -->
<form method="POST" action="registro.php">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" required>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required>

    <label for="tipo_usuario">Tipo de Usuario:</label>
    <select name="tipo_usuario" id="tipo_usuario" required>
        <option value="encargado">Encargado</option>
        <option value="admin">Administrador</option>
    </select>

    <label for="sucursal">Sucursal:</label>
    <select name="sucursal" id="sucursal" required>
        <option value="Sucursal A">Sucursal A</option>
        <option value="Sucursal B">Sucursal B</option>
    </select>

    <button type="submit">Registrar Usuario</button>
</form>

<script>
    // Cuando el tipo de usuario cambie, desactivar la sucursal si es administrador
    document.getElementById('tipo_usuario').addEventListener('change', function() {
        var tipoUsuario = this.value;
        var sucursalSelect = document.getElementById('sucursal');

        if (tipoUsuario == 'admin') {
            // Desactivar sucursal y asignar NULL
            sucursalSelect.disabled = true;
            sucursalSelect.value = ''; // Asignar valor vacío para enviar NULL
        } else {
            // Activar sucursal
            sucursalSelect.disabled = false;
        }
    });
</script>
