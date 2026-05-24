<?php
include("conexao.php");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_sensor = $_POST["tipo_sensor"] ?? "";

    if ($tipo_sensor != "") {
        $stmt = $conn->prepare("DELETE FROM leituras_sensores WHERE tipo_sensor = ?");
        $stmt->bind_param("s", $tipo_sensor);

        if ($stmt->execute()) {
            $mensagem = "Datos del sensor " . htmlspecialchars($tipo_sensor) . " eliminados correctamente.";
        } else {
            $mensagem = "Error al eliminar los datos.";
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Datos de los Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Gestionar Datos</h1>

    <?php if ($mensagem != ""): ?>
        <div class="mensagem-sucesso">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <h2>Registros almacenados</h2>

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
        <p>No hay datos almacenados.</p>
    <?php endif; ?>

    <form method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar los datos de este sensor?');">
        <label>Seleccione el sensor que desea eliminar:</label>

        <select name="tipo_sensor" required>
            <option value="">Seleccione</option>
            <option value="Magnetometro">Magnetómetro</option>
            <option value="Giroscopio">Giroscopio</option>
            <option value="Acelerometro">Acelerómetro</option>
            <option value="MontanaRusa">Montaña rusa</option>
        </select>

        <button type="submit" class="btn-danger">Eliminar datos del sensor</button>
    </form>

    <a href="index.php">Volver a la importación</a>
    <a href="resultados.php">Ver resultados</a>
</div>

</body>
</html>