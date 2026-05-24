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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de los Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container grande">
    <h1>Datos de los Sensores</h1>

    <form method="GET">
        <label>Seleccione el sensor:</label>
        <select name="tipo_sensor">
            <option value="">Todos</option>
            <option value="Magnetómetro" <?php if($tipo=="Magnetómetro") echo "selected"; ?>>Magnetómetro</option>
            <option value="Giroscopio" <?php if($tipo=="Giroscopio") echo "selected"; ?>>Giroscopio</option>
            <option value="Acelerómetro" <?php if($tipo=="Acelerómetro") echo "selected"; ?>>Acelerómetro</option>
            <option value="Montaña Rusa" <?php if($tipo=="Montaña Rusa") echo "selected"; ?>>Montaña rusa</option>
        </select>

        <button type="submit">Visualizar datos</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Sensor</th>
            <th>Tiempo</th>
            <th>Eje X</th>
            <th>Eje Y</th>
            <th>Eje Z</th>
            <th>Total</th>
            <th>Fecha de importación</th>
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

    <a href="index.php">Volver</a>
</div>

</body>
</html>