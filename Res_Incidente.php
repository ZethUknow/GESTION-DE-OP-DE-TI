<?php
session_start();
include 'db.php';

$conn = openConnection();

if ($_SESSION['tipo_usuario'] != 'admin') {
    header('Location: login.php');
    exit();
}

$id_incidente = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_incidente === 0) {
    echo "Incidente no encontrado.";
    exit();
}

// Obtener los datos del incidente
$query = $conn->prepare("SELECT * FROM incidentes WHERE id_incidente = ?");
$query->bind_param("i", $id_incidente);
$query->execute();
$result = $query->get_result();
$incidente = $result->fetch_assoc();

$tecnico_asignado = isset($incidente['id_tecnico_asignado']) && $incidente['id_tecnico_asignado'] !== null
    ? $incidente['id_tecnico_asignado']
    : -1;

$tecnicos_result = $conn->query("SELECT * FROM tecnicos WHERE (disponibilidad = 'Disponible' OR id_tecnico = $tecnico_asignado)");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $estado = $_POST['estado'];
    $id_tecnico = intval($_POST['id_tecnico']);
    $prioridad = $_POST['prioridad'];
    $resolucion = trim($_POST['resolucion']);

    $dias_refaccion = 0;
    if (isset($_POST['usar_refaccion']) && $_POST['usar_refaccion'] === 'on') {
        if (isset($_POST['dias_refaccion']) && is_numeric($_POST['dias_refaccion']) && intval($_POST['dias_refaccion']) > 0) {
            $dias_refaccion = intval($_POST['dias_refaccion']);
        }
    }

    switch (strtolower($prioridad)) {
        case 'baja': $dias_prioridad = 4; break;
        case 'media': $dias_prioridad = 3; break;
        case 'alta': $dias_prioridad = 2; break;
        case 'crítica': $dias_prioridad = 1; break;
        default: $dias_prioridad = 3; break;
    }

    $fecha_limite_existente = $incidente['fecha_limite'];

    if (is_null($fecha_limite_existente)) {
    $dias_totales = $dias_prioridad + $dias_refaccion;
    $fecha_limite = date('Y-m-d', strtotime("+".($dias_totales - 1)." days"));
} else {
    $fecha_limite = $fecha_limite_existente;
}


    if ($id_tecnico != $tecnico_asignado && $tecnico_asignado != -1) {
        $update_tecnico_query = $conn->prepare("UPDATE tecnicos SET disponibilidad = 'Disponible' WHERE id_tecnico = ?");
        $update_tecnico_query->bind_param("i", $tecnico_asignado);
        $update_tecnico_query->execute();
    }

    if ($resolucion === '') {
        $resolucion = null;
    }

    $update_query = $conn->prepare("UPDATE incidentes SET estado = ?, id_tecnico_asignado = ?, prioridad = ?, fecha_limite = ?, resolucion = ? WHERE id_incidente = ?");
    $update_query->bind_param("sisssi", $estado, $id_tecnico, $prioridad, $fecha_limite, $resolucion, $id_incidente);
    $update_query->execute();

    if ($id_tecnico != -1) {
        if (in_array($estado, ['rechazada', 'terminado', 'liberado', 'evaluado'])) {
            $disponibilidad = 'Disponible';
        } elseif ($estado == 'en proceso') {
            $disponibilidad = 'En servicio';
        } else {
            $disponibilidad = null;
        }

        if ($disponibilidad !== null) {
            $update_tecnico_query = $conn->prepare("UPDATE tecnicos SET disponibilidad = ? WHERE id_tecnico = ?");
            $update_tecnico_query->bind_param("si", $disponibilidad, $id_tecnico);
            $update_tecnico_query->execute();
        }
    }

    if ($estado == 'terminado') {
        $fecha_resolucion = date('Y-m-d H:i:s');
        $update_fecha_resolucion_query = $conn->prepare("UPDATE incidentes SET fecha_resolucion = ? WHERE id_incidente = ?");
        $update_fecha_resolucion_query->bind_param("si", $fecha_resolucion, $id_incidente);
        $update_fecha_resolucion_query->execute();
    }

    echo "<script>alert('Incidente actualizado correctamente'); window.location.href='gestionar_incidentes.php';</script>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestionar Incidente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="style/styles.css" />
    <script>
        function mostrarDiasRefaccion() {
            const checkbox = document.getElementById('usar_refaccion');
            const campoDias = document.getElementById('campo_dias_refaccion');
            const inputDias = document.getElementById('dias_refaccion');

            if (checkbox.checked) {
                campoDias.style.display = 'block';
                inputDias.disabled = false;
            } else {
                campoDias.style.display = 'none';
                inputDias.disabled = true;
                inputDias.value = ''; // Limpiar el valor si no está seleccionado
            }
        }
    </script>
</head>
<body>

<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="admin_dashboard.php">Inicio</a>
        <a href="base_conocimiento.php">Base de conocimiento</a>
        <a href="admin_empleados.php">Empleados</a>
        <a href="admin_encargados.php">Encargados</a>
        <a href="Admin_Equipo.php">Equipo</a>
        <a href="soli_pend.php">Solicitudes pendientes</a>
        <a href="gestionar_incidentes.php" class="active">Gestionar Incidentes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <div class="contenedor-principal">
        <h1 class="page-title">Editar Incidente</h1>

        <form method="POST" class="formulario-editar">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required class="input-text">
                <?php
                $estados = ['enviado', 'en proceso', 'rechazada'];
                foreach ($estados as $estado) {
                    $selected = ($incidente['estado'] == $estado) ? 'selected' : '';
                    echo "<option value=\"$estado\" $selected>" . ucfirst($estado) . "</option>";
                }
                ?>
            </select><br><br>

            <label for="prioridad">Prioridad:</label>
            <select name="prioridad" id="prioridad" required class="input-text">
                <?php
                $prioridades = ['Baja', 'Media', 'Alta', 'Crítica'];
                foreach ($prioridades as $p) {
                    $selected = ($incidente['prioridad'] == $p) ? 'selected' : '';
                    echo "<option value=\"$p\" $selected>$p</option>";
                }
                ?>
            </select><br><br>

            <label for="id_tecnico">Técnico Asignado:</label>
            <select name="id_tecnico" id="id_tecnico" required class="input-text">
                <option value="-1" <?= $tecnico_asignado == -1 ? 'selected' : '' ?>>No asignado</option>
                <?php while ($tecnico = $tecnicos_result->fetch_assoc()): ?>
                    <option value="<?= $tecnico['id_tecnico'] ?>" <?= $tecnico['id_tecnico'] == $tecnico_asignado ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tecnico['nombre']) ?> (<?= htmlspecialchars($tecnico['especialidad']) ?>)
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <button type="submit" class="btn btn-yellow">Actualizar Incidente</button>
        </form>

        <br>
        <a href="gestionar_incidentes.php" class="btn btn-back">Volver a Lista de Incidentes</a>
    </div>
</main>

</body>
</html>
