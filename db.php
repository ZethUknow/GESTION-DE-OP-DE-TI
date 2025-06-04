<?php
function openConnection() {
    // Parámetros de conexión
    $host = "localhost";       // Dirección del servidor de base de datos
    $puerto = "3305";          // Puerto de conexión
    $usuario = "root";         // Usuario de MySQL
    $contrasena = "";          // Contraseña de MySQL (vacía en este caso)
    $base_de_datos = "gestion_incidentes"; // Nombre de la base de datos

    // Crear conexión usando los parámetros especificados
    $conn = new mysqli($host, $usuario, $contrasena, $base_de_datos, $puerto);

    // Comprobar si hubo algún error en la conexión
    if ($conn->connect_error) {
        die("Error en la conexión: " . $conn->connect_error);
    }
    return $conn;
}

function closeConnection($conn) {
    $conn->close();
}
?>
