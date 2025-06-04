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

$stmt = $conn->prepare("SELECT i.*, ci.nombre AS ci_nombre, ci.id_ci, ci.responsable AS ci_responsable, ci.procesador, ci.ram, ci.display, u.nombre AS encargado_nombre, u.sucursal AS sucursal_nombre FROM incidentes i LEFT JOIN ci_items ci ON i.id_ci_afectado = ci.id_ci LEFT JOIN usuarios u ON i.responsable = u.nombre WHERE i.id_incidente = ? AND i.id_tecnico_asignado = ?");
$stmt->bind_param("ii", $id_incidente, $id_tecnico);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Incidente no encontrado o no tienes acceso.";
    exit();
}

$incidente = $result->fetch_assoc();
$catalogo = $conn->query("SELECT * FROM catalogo_servicios WHERE estado = 'activo'");

// Servicios asignados
$asignados = $conn->prepare("SELECT si.*, cs.nombre_servicio FROM servicios_incidente si JOIN catalogo_servicios cs ON si.id_servicio = cs.id_servicio WHERE si.id_incidente = ?");
$asignados->bind_param("i", $id_incidente);
$asignados->execute();
$servicios_result = $asignados->get_result();

// Recursos asignados
$recursos_stmt = $conn->prepare("SELECT ri.*, cr.nombre_recurso, cr.unidad FROM recursos_incidente ri JOIN catalogo_recursos cr ON ri.id_recurso = cr.id_recurso WHERE ri.id_incidente = ?");
$recursos_stmt->bind_param("i", $id_incidente);
$recursos_stmt->execute();
$recursos_result = $recursos_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Atender Incidente #<?= $id_incidente ?></title>
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
<div class="page-wrapper">
    <div class="main-container">
        <h2 style="color: #ffcc00;">Atendiendo Incidente #<?= $id_incidente ?></h2>

        <h3>Información del Incidente</h3>
        <p><strong>Descripción:</strong> <?= htmlspecialchars($incidente['descripcion']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($incidente['estado']) ?></p>
        <p><strong>Reportado por:</strong> <?= htmlspecialchars($incidente['encargado_nombre']) ?> (<?= htmlspecialchars($incidente['sucursal_nombre']) ?>)</p>

        <h3>CI Afectado</h3>
        <ul>
            <li><strong>Nombre:</strong> <?= htmlspecialchars($incidente['ci_nombre']) ?></li>
            <li><strong>Responsable:</strong> <?= htmlspecialchars($incidente['ci_responsable']) ?></li>
            <li><strong>Procesador:</strong> <?= htmlspecialchars($incidente['procesador']) ?></li>
            <li><strong>RAM:</strong> <?= htmlspecialchars($incidente['ram']) ?></li>
            <li><strong>Display:</strong> <?= htmlspecialchars($incidente['display']) ?></li>
        </ul>

        <hr>

        <h3>Diagnóstico</h3>
        <form id="form-diagnostico">
            <input type="hidden" name="id_incidente" value="<?= $id_incidente ?>">
            <label for="diagnostico">Descripción del diagnóstico:</label><br>
            <textarea name="diagnostico" id="diagnostico" rows="5" required><?= htmlspecialchars($incidente['diagnostico'] ?? '') ?></textarea><br><br>
            <button type="submit" class="btn">Guardar Diagnóstico</button>
        </form>
        <div id="mensaje-diagnostico" style="display:none; color:green; margin-top:10px; font-weight:bold;"></div>

        <h3>Tiempo Estimado Total</h3>
        <p><strong><?= $incidente['tiempo_estimado_total'] ?? 0 ?> minutos</strong></p>
        <div id="lista-servicios-asignados">
            <?php if ($servicios_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $servicios_result->fetch_assoc()): ?>
                        <li><strong><?= htmlspecialchars($row['nombre_servicio']) ?></strong> (<?= htmlspecialchars($row['fecha_registro']) ?>)</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No hay servicios asignados aún.</p>
            <?php endif; ?>
        </div>

        <h3>Recursos (Refacciones) asignados</h3>
        <div id="lista-recursos-asignados">
            <?php if ($recursos_result->num_rows > 0): ?>
                <ul>
                    <?php while ($recurso = $recursos_result->fetch_assoc()): ?>
                        <li><strong><?= htmlspecialchars($recurso['nombre_recurso']) ?></strong> - Cantidad: <?= $recurso['cantidad_utilizada'] ?> <?= htmlspecialchars($recurso['unidad']) ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Sin recursos asignados.</p>
            <?php endif; ?>
        </div>

        <h3>Buscar y Solicitar Recurso</h3>
        <input type="text" id="buscar_recurso" placeholder="Buscar recurso..." onkeyup="buscarRecurso()" />
        <select id="select_recurso"></select>
        <br>
        <label for="cantidad_recurso">Cantidad:</label>
        <input type="number" id="cantidad_recurso" value="1" min="1" />
        <button onclick="asignarRecurso()" class="btn">Solicitar Recurso</button>
        <div id="mensaje-recurso" style="color:green; font-weight:bold; margin-top:10px;"></div>

        <h3>Asignar un Servicio</h3>
        <label for="id_servicio">Servicio:</label><br>
        <select name="id_servicio" id="id_servicio">
            <option value="">Selecciona un servicio...</option>
            <?php 
            mysqli_data_seek($catalogo, 0);
            while ($servicio = $catalogo->fetch_assoc()): ?>
                <option value="<?= $servicio['id_servicio'] ?>">
                    <?= htmlspecialchars($servicio['nombre_servicio']) ?> - $<?= number_format($servicio['costo'], 2) ?> - <?= $servicio['tiempo_estimado_minutos'] ?> min
                </option>
            <?php endwhile; ?>
        </select>
        <button type="button" onclick="asignarServicio()" class="btn">Asignar Servicio</button>
        <div id="mensaje-servicio" style="display:none; color:green; margin-top:10px; font-weight:bold;"></div>

        <br><br>
        <a href="solicitar_servicio_nuevo.php?id_tecnico=<?= $id_tecnico ?>&id_incidente=<?= $id_incidente ?>" 
           class="btn">
            Solicitar servicio nuevo
        </a>
    </div>
</div>

<script>
document.getElementById("form-diagnostico").addEventListener("submit", function(e) {
    e.preventDefault();
    const form = new FormData(this);

    fetch("guardar_diagnostico.php", {
        method: "POST",
        body: form
    })
    .then(response => response.json())
    .then(data => {
        const mensaje = document.getElementById("mensaje-diagnostico");
        if (data.success) {
            mensaje.textContent = data.mensaje;
            mensaje.style.display = "block";
            setTimeout(() => location.reload(), 1500);
        } else {
            alert("Error: " + (data.error || "No se pudo guardar"));
        }
    });
});

function buscarRecurso() {
    const query = document.getElementById('buscar_recurso').value;
    if (query.length < 2) return;

    fetch('buscar_recurso.php?query=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('select_recurso');
            select.innerHTML = '';
            data.forEach(recurso => {
                const option = document.createElement('option');
                option.value = recurso.id_recurso;
                option.textContent = recurso.nombre_recurso + " (" + recurso.cantidad_disponible + " disponibles)";
                select.appendChild(option);
            });
        });
}

function asignarRecurso() {
    const idRecurso = document.getElementById('select_recurso').value;
    const cantidad = document.getElementById('cantidad_recurso').value;

    if (!idRecurso || cantidad <= 0) return;

    const formData = new FormData();
    formData.append('id_incidente', <?= $id_incidente ?>);
    formData.append('id_recurso', idRecurso);
    formData.append('cantidad', cantidad);

    fetch('asignar_recurso.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('mensaje-recurso');
        msg.textContent = data.success ? "Solicitud registrada correctamente" : ("Error: " + data.error);
        msg.style.color = data.success ? "green" : "red";
        if (data.success) {
            setTimeout(() => location.reload(), 1500);
        }
    });
}

function asignarServicio() {
    const idServicio = document.getElementById('id_servicio').value;
    const mensaje = document.getElementById('mensaje-servicio');

    if (!idServicio) return;

    const formData = new FormData();
    formData.append('id_incidente', <?= $id_incidente ?>);
    formData.append('id_servicio', idServicio);

    fetch('asignar_servicio.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mensaje.textContent = "✅ Servicio asignado correctamente";
            mensaje.style.display = "block";
            setTimeout(() => location.reload(), 1500);
        } else {
            alert(data.error);
        }
    });
}
</script>
</body>
</html>
