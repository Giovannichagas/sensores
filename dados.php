<?php
include("conexao.php");

$tipo = $_GET["tipo_sensor"] ?? "";

$sql = "SELECT * FROM leituras_sensores";
$params = [];

if ($tipo != "") {
    $sql .= " WHERE tipo_sensor = ?";
}

$sql .= " ORDER BY data_importacao DESC, id DESC LIMIT 200";

$stmt = $conn->prepare($sql);

if ($tipo != "") {
    $stmt->bind_param("s", $tipo);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dados dos Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container grande">
    <h1>Dados dos Sensores</h1>

    <form method="GET">
        <label>Escolha o sensor:</label>
        <select name="tipo_sensor">
            <option value="">Todos</option>
            <option value="Magnetometro" <?php if($tipo=="Magnetometro") echo "selected"; ?>>Magnetômetro</option>
            <option value="Giroscopio" <?php if($tipo=="Giroscopio") echo "selected"; ?>>Giroscópio</option>
            <option value="Acelerometro" <?php if($tipo=="Acelerometro") echo "selected"; ?>>Acelerômetro</option>
            <option value="Acelerometro" <?php if($tipo=="MontanaRusa") echo "selected"; ?>>Motaña Rusa</option>
        </select>

        <button type="submit">Visualizar dados</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Sensor</th>
            <th>Tempo</th>
            <th>Eixo X</th>
            <th>Eixo Y</th>
            <th>Eixo Z</th>
            <th>Total</th>
            <th>Data importação</th>
        </tr>

        <?php while($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo htmlspecialchars($row["tipo_sensor"]); ?></td>
                <td><?php echo $row["tempo"]; ?></td>
                <td><?php echo $row["eixo_x"]; ?></td>
                <td><?php echo $row["eixo_y"]; ?></td>
                <td><?php echo $row["eixo_z"]; ?></td>
                <td><?php echo $row["total"]; ?></td>
                <td><?php echo $row["data_importacao"]; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="index.php">Voltar</a>
</div>

</body>
</html>