<?php
session_start();
include 'db.php';

$conn = openConnection();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'encargado') {
    header("Location: login.php");
    exit();
}

$id_incidente = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_incidente <= 0) {
    echo "ID de incidente inválido.";
    exit();
}

// Verificamos que el incidente esté en estado 'terminado' y no haya sido evaluado aún
$stmt = $conn->prepare("SELECT i.id_incidente, i.estado, i.titulo, i.descripcion FROM incidentes i WHERE i.id_incidente = ? AND i.estado = 'terminado'");
$stmt->bind_param("i", $id_incidente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Este incidente no está disponible para evaluación.";
    exit();
}

$incidente = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluar Incidente</title>
    <link rel="stylesheet" href="style/styles.css">
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 2em;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: gold;
        }

    </style>
</head>
<body>
<header class="dashboard-header">
    <nav class="dashboard-nav">
        <a href="dashboard_encargado.php">Inicio</a>
         <a href="reportar_incidente.php">Reportar Incidente</a>
        <a href="Ver_Reportes.php" class="active">Mis Reportes</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

<main class="main-container">
    <div class="form-box">
        <h2>Evaluar Incidente #<?= $incidente['id_incidente'] ?></h2>
        <p><strong>Título:</strong> <?= htmlspecialchars($incidente['titulo']) ?></p>
        <p><strong>Descripción:</strong> <?= htmlspecialchars($incidente['descripcion']) ?></p>

        <form action="evaluar_incidente.php" method="POST">
            <input type="hidden" name="id_incidente" value="<?= $incidente['id_incidente'] ?>">

            <label><strong>¿Qué tan satisfecho estás con la resolución?</strong></label>
            <div class="rating">
                <input type="radio" name="estrellas" id="star5" value="5"><label for="star5">★</label>
                <input type="radio" name="estrellas" id="star4" value="4"><label for="star4">★</label>
                <input type="radio" name="estrellas" id="star3" value="3"><label for="star3">★</label>
                <input type="radio" name="estrellas" id="star2" value="2"><label for="star2">★</label>
                <input type="radio" name="estrellas" id="star1" value="1" required><label for="star1">★</label>
            </div>

            <br>
            <label for="comentario"><strong>Comentarios (opcional):</strong></label><br>
            <textarea name="comentario" id="comentario" rows="5" style="width: 100%;"></textarea>

            <br><br>
            <button type="submit" class="btn">Enviar Evaluación</button>
        </form>
    </div>
</main>
</body>
</html>
