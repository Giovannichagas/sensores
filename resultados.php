<?php
include("conexao.php");

$tipo = $_GET["tipo_sensor"] ?? "";

$resultado = null;

if ($tipo != "") {
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) AS total_registros,
            AVG(eixo_x) AS media_x,
            AVG(eixo_y) AS media_y,
            AVG(eixo_z) AS media_z,
            AVG(total) AS media_total
        FROM leituras_sensores
        WHERE tipo_sensor = ?
    ");

    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de los Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Resultados de los Sensores</h1>

    <?php if (isset($_GET["sucesso"])): ?>
        <div class="mensagem-sucesso">
            ¡CSV importado correctamente! 
            <?php echo intval($_GET["linhas"]); ?> líneas se han guardado en la base de datos.
        </div>
    <?php endif; ?>

    <form method="GET">
        <label>Seleccione el sensor:</label>
        <select name="tipo_sensor" required>
            <option value="">Seleccione</option>
            <option value="Magnetometro">Magnetómetro</option>
            <option value="Giroscopio">Giroscopio</option>
            <option value="Acelerometro">Acelerómetro</option>
            <option value="MontanaRusa">Montaña rusa</option>
        </select>

        <button type="submit">Buscar medias</button>
    </form>

    <?php if ($resultado): ?>
        <div class="card">
            <h2><?php echo htmlspecialchars($tipo); ?></h2>
            <p>Total de registros: <?php echo $resultado["total_registros"]; ?></p>
            <p>Media eje X: <?php echo number_format($resultado["media_x"], 4, ",", "."); ?></p>
            <p>Media eje Y: <?php echo number_format($resultado["media_y"], 4, ",", "."); ?></p>
            <p>Media eje Z: <?php echo number_format($resultado["media_z"], 4, ",", "."); ?></p>
            <p>Media total: <?php echo number_format($resultado["media_total"], 4, ",", "."); ?></p>
        </div>
    <?php endif; ?>

    <a href="index.php">Volver</a>
</div>

</body>
</html>