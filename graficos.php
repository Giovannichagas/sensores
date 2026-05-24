<?php
include("conexao.php");

$tipo = $_GET["tipo_sensor"] ?? "";
$limite = $_GET["limite"] ?? 100;

$limites_permitidos = [50, 100, 200, 500];

if (!in_array((int)$limite, $limites_permitidos)) {
    $limite = 100;
}

$sql = "SELECT * FROM leituras_sensores";

if ($tipo != "") {
    $sql .= " WHERE tipo_sensor = ?";
}

$sql .= " ORDER BY id DESC LIMIT ?";

$stmt = $conn->prepare($sql);

if ($tipo != "") {
    $stmt->bind_param("si", $tipo, $limite);
} else {
    $stmt->bind_param("i", $limite);
}

$stmt->execute();
$resultado = $stmt->get_result();

$tempos = [];
$eixo_x = [];
$eixo_y = [];
$eixo_z = [];
$total = [];

while ($row = $resultado->fetch_assoc()) {
    $tempos[] = $row["tempo"];
    $eixo_x[] = (float)$row["eixo_x"];
    $eixo_y[] = (float)$row["eixo_y"];
    $eixo_z[] = (float)$row["eixo_z"];
    $total[] = (float)$row["total"];
}

$tempos = array_reverse($tempos);
$eixo_x = array_reverse($eixo_x);
$eixo_y = array_reverse($eixo_y);
$eixo_z = array_reverse($eixo_z);
$total = array_reverse($total);

function media($array) {
    return count($array) > 0 ? array_sum($array) / count($array) : 0;
}

$mediaTotal = media($total);
$maxTotal = count($total) > 0 ? max($total) : 0;
$minTotal = count($total) > 0 ? min($total) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gráficos de los Sensores</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .grid-graficos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 30px;
        }

        .box-grafico {
            background: #ffffff;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 0 12px rgba(0,0,0,0.12);
        }

        .box-grafico h2 {
            font-size: 20px;
            margin-bottom: 8px;
            text-align: center;
        }

        .legenda-grafico {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .box-grafico canvas {
            width: 100% !important;
            height: 320px !important;
        }

        .cards-resumo {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 25px;
        }

        .card-resumo {
            background: #ffffff;
            padding: 20px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 0 12px rgba(0,0,0,0.12);
        }

        .card-resumo h3 {
            margin-bottom: 10px;
        }

        .card-resumo p {
            font-size: 26px;
            font-weight: bold;
        }

        @media (max-width: 900px) {
            .grid-graficos {
                grid-template-columns: 1fr;
            }

            .cards-resumo {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container grande">
    <h1>Gráficos de los Sensores</h1>

    <form method="GET">
        <label>Seleccione el sensor:</label>

        <select name="tipo_sensor">
            <option value="">Todos</option>
            <option value="Magnetometro" <?php if($tipo=="Magnetometro") echo "selected"; ?>>Magnetómetro</option>
            <option value="Giroscopio" <?php if($tipo=="Giroscopio") echo "selected"; ?>>Giroscopio</option>
            <option value="Acelerometro" <?php if($tipo=="Acelerometro") echo "selected"; ?>>Acelerómetro</option>
            <option value="MontanaRusa" <?php if($tipo=="MontanaRusa") echo "selected"; ?>>Montaña rusa</option>
        </select>

        <label>Cantidad:</label>

        <select name="limite">
            <option value="50" <?php if($limite==50) echo "selected"; ?>>50 registros</option>
            <option value="100" <?php if($limite==100) echo "selected"; ?>>100 registros</option>
            <option value="200" <?php if($limite==200) echo "selected"; ?>>200 registros</option>
            <option value="500" <?php if($limite==500) echo "selected"; ?>>500 registros</option>
        </select>

        <button type="submit">Filtrar</button>
    </form>

    <div class="cards-resumo">
        <div class="card-resumo">
            <h3>Media total</h3>
            <p><?php echo number_format($mediaTotal, 2, ",", "."); ?></p>
        </div>

        <div class="card-resumo">
            <h3>Valor máximo</h3>
            <p><?php echo number_format($maxTotal, 2, ",", "."); ?></p>
        </div>

        <div class="card-resumo">
            <h3>Valor mínimo</h3>
            <p><?php echo number_format($minTotal, 2, ",", "."); ?></p>
        </div>
    </div>

    <div class="grid-graficos">
        <div class="box-grafico">
            <h2>Evolución de los sensores</h2>
            <p class="legenda-grafico">
                Muestra cómo los valores de los ejes X, Y, Z y Total varían a lo largo del tiempo.
            </p>
            <canvas id="graficoLinha"></canvas>
        </div>

        <div class="box-grafico">
            <h2>Comparación de los ejes</h2>
            <p class="legenda-grafico">
                Compara la media de los valores de los ejes X, Y, Z y Total en el conjunto de datos filtrado.
            </p>
            <canvas id="graficoBarras"></canvas>
        </div>

        <div class="box-grafico">
            <h2>Dispersión del valor total</h2>
            <p class="legenda-grafico">
                Muestra la distribución de los valores totales y ayuda a identificar picos o valores fuera de lo habitual.
            </p>
            <canvas id="graficoDispersao"></canvas>
        </div>

        <div class="box-grafico">
            <h2>Distribución de las medias</h2>
            <p class="legenda-grafico">
                Presenta la participación media de cada eje en relación con el total de los datos seleccionados.
            </p>
            <canvas id="graficoPizza"></canvas>
        </div>
    </div>

    <br>
    <a href="index.php">Volver</a>
</div>

<script>
const tempos = <?php echo json_encode($tempos); ?>;
const eixoX = <?php echo json_encode($eixo_x); ?>;
const eixoY = <?php echo json_encode($eixo_y); ?>;
const eixoZ = <?php echo json_encode($eixo_z); ?>;
const total = <?php echo json_encode($total); ?>;

function calcularMedia(lista) {
    if (lista.length === 0) return 0;
    const soma = lista.reduce((a, b) => a + b, 0);
    return soma / lista.length;
}

new Chart(document.getElementById('graficoLinha'), {
    type: 'line',
    data: {
        labels: tempos,
        datasets: [
            { label: 'Eje X', data: eixoX },
            { label: 'Eje Y', data: eixoY },
            { label: 'Eje Z', data: eixoZ },
            { label: 'Total', data: total }
        ]
    }
});

new Chart(document.getElementById('graficoBarras'), {
    type: 'bar',
    data: {
        labels: ['Eje X', 'Eje Y', 'Eje Z', 'Total'],
        datasets: [{
            label: 'Media de los valores',
            data: [
                calcularMedia(eixoX),
                calcularMedia(eixoY),
                calcularMedia(eixoZ),
                calcularMedia(total)
            ]
        }]
    }
});

new Chart(document.getElementById('graficoDispersao'), {
    type: 'scatter',
    data: {
        datasets: [{
            label: 'Dispersión del Total',
            data: total.map((valor, index) => ({
                x: index + 1,
                y: valor
            }))
        }]
    }
});

new Chart(document.getElementById('graficoPizza'), {
    type: 'doughnut',
    data: {
        labels: ['Eje X', 'Eje Y', 'Eje Z', 'Total'],
        datasets: [{
            label: 'Distribución de las medias',
            data: [
                calcularMedia(eixoX),
                calcularMedia(eixoY),
                calcularMedia(eixoZ),
                calcularMedia(total)
            ]
        }]
    }
});
</script>

</body>
</html>