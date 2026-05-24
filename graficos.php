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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gráficos dos Sensores</title>
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
    <h1>Gráficos dos Sensores</h1>

    <form method="GET">
        <label>Escolha o sensor:</label>

        <select name="tipo_sensor">
            <option value="">Todos</option>
            <option value="Magnetometro" <?php if($tipo=="Magnetometro") echo "selected"; ?>>Magnetômetro</option>
            <option value="Giroscopio" <?php if($tipo=="Giroscopio") echo "selected"; ?>>Giroscópio</option>
            <option value="Acelerometro" <?php if($tipo=="Acelerometro") echo "selected"; ?>>Acelerômetro</option>
            <option value="MontanaRusa" <?php if($tipo=="MontanaRusa") echo "selected"; ?>>Montaña Rusa</option>
        </select>

        <label>Quantidade:</label>

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
            <h3>Média Total</h3>
            <p><?php echo number_format($mediaTotal, 2, ",", "."); ?></p>
        </div>

        <div class="card-resumo">
            <h3>Valor Máximo</h3>
            <p><?php echo number_format($maxTotal, 2, ",", "."); ?></p>
        </div>

        <div class="card-resumo">
            <h3>Valor Mínimo</h3>
            <p><?php echo number_format($minTotal, 2, ",", "."); ?></p>
        </div>
    </div>

    <div class="grid-graficos">
        <div class="box-grafico">
            <h2>Evolução dos sensores</h2>
            <p class="legenda-grafico">
                Mostra como os valores dos eixos X, Y, Z e Total variam ao longo do tempo.
            </p>
            <canvas id="graficoLinha"></canvas>
        </div>

        <div class="box-grafico">
            <h2>Comparação dos eixos</h2>
            <p class="legenda-grafico">
                Compara a média dos valores dos eixos X, Y, Z e Total no conjunto de dados filtrado.
            </p>
            <canvas id="graficoBarras"></canvas>
        </div>

        <div class="box-grafico">
            <h2>Dispersão do valor total</h2>
            <p class="legenda-grafico">
                Mostra a distribuição dos valores totais e ajuda a identificar picos ou valores fora do padrão.
            </p>
            <canvas id="graficoDispersao"></canvas>
        </div>

        <div class="box-grafico">
            <h2>Distribuição das médias</h2>
            <p class="legenda-grafico">
                Apresenta a participação média de cada eixo em relação ao total dos dados selecionados.
            </p>
            <canvas id="graficoPizza"></canvas>
        </div>
    </div>

    <br>
    <a href="index.php">Voltar</a>
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
            { label: 'Eixo X', data: eixoX },
            { label: 'Eixo Y', data: eixoY },
            { label: 'Eixo Z', data: eixoZ },
            { label: 'Total', data: total }
        ]
    }
});

new Chart(document.getElementById('graficoBarras'), {
    type: 'bar',
    data: {
        labels: ['Eixo X', 'Eixo Y', 'Eixo Z', 'Total'],
        datasets: [{
            label: 'Média dos valores',
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
            label: 'Dispersão do Total',
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
        labels: ['Eixo X', 'Eixo Y', 'Eixo Z', 'Total'],
        datasets: [{
            label: 'Distribuição das médias',
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