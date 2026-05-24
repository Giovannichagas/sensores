<?php
include("conexao.php");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_sensor = $_POST["tipo_sensor"] ?? "";

    if ($tipo_sensor != "") {
        $stmt = $conn->prepare("DELETE FROM leituras_sensores WHERE tipo_sensor = ?");
        $stmt->bind_param("s", $tipo_sensor);

        if ($stmt->execute()) {
            $mensagem = "Dados do sensor " . htmlspecialchars($tipo_sensor) . " apagados com sucesso!";
        } else {
            $mensagem = "Erro ao apagar os dados.";
        }
    }
}

$consulta = $conn->query("
    SELECT tipo_sensor, COUNT(*) AS total
    FROM leituras_sensores
    GROUP BY tipo_sensor
");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Dados dos Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Gerenciar Dados</h1>

    <?php if ($mensagem != ""): ?>
        <div class="mensagem-sucesso">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <h2>Registros armazenados</h2>

    <?php if ($consulta->num_rows > 0): ?>
        <table>
            <tr>
                <th>Sensor</th>
                <th>Total de registros</th>
            </tr>

            <?php while ($row = $consulta->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["tipo_sensor"]); ?></td>
                    <td><?php echo $row["total"]; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Nenhum dado armazenado.</p>
    <?php endif; ?>

    <form method="POST" onsubmit="return confirm('Tem certeza que deseja apagar os dados deste sensor?');">
        <label>Escolha o sensor para apagar:</label>

        <select name="tipo_sensor" required>
            <option value="">Selecione</option>
            <option value="Magnetometro">Magnetômetro</option>
            <option value="Giroscopio">Giroscópio</option>
            <option value="Acelerometro">Acelerômetro</option>
            <option value="Acelerometro">Motaña Rusa</option>
        </select>

        <button type="submit" class="btn-danger">Apagar dados do sensor</button>
    </form>

    <a href="index.php">Voltar para importação</a>
    <a href="resultados.php">Ver resultados</a>
</div>

</body>
</html>