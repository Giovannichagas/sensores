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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Resultados dos Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Resultados dos Sensores</h1>

    <?php if (isset($_GET["sucesso"])): ?>
        <div class="mensagem-sucesso">
            CSV importado com sucesso! 
            <?php echo intval($_GET["linhas"]); ?> linhas foram salvas no banco de dados.
        </div>
    <?php endif; ?>

    <form method="GET">
        <label>Escolha o sensor:</label>
        <select name="tipo_sensor" required>
            <option value="">Selecione</option>
            <option value="Magnetometro">Magnetômetro</option>
            <option value="Giroscopio">Giroscópio</option>
            <option value="Acelerometro">Acelerômetro</option>
            <option value="Acelerometro">Motaña Rusa</option>
        </select>

        <button type="submit">Buscar médias</button>
    </form>

    <?php if ($resultado): ?>
        <div class="card">
            <h2><?php echo htmlspecialchars($tipo); ?></h2>
            <p>Total de registros: <?php echo $resultado["total_registros"]; ?></p>
            <p>Média eixo X: <?php echo number_format($resultado["media_x"], 4, ",", "."); ?></p>
            <p>Média eixo Y: <?php echo number_format($resultado["media_y"], 4, ",", "."); ?></p>
            <p>Média eixo Z: <?php echo number_format($resultado["media_z"], 4, ",", "."); ?></p>
            <p>Média total: <?php echo number_format($resultado["media_total"], 4, ",", "."); ?></p>
        </div>
    <?php endif; ?>

    <a href="index.php">Voltar</a>
</div>

</body>
</html>