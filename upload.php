<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tipo_sensor = $_POST["tipo_sensor"] ?? "";

    if (isset($_FILES["arquivo_csv"]) && $_FILES["arquivo_csv"]["error"] == 0) {

        $arquivo = $_FILES["arquivo_csv"]["tmp_name"];

        if (($handle = fopen($arquivo, "r")) !== false) {

            fgetcsv($handle, 1000, ";");

            $linhas_importadas = 0;

            while (($dados = fgetcsv($handle, 1000, ";")) !== false) {

                if (count($dados) >= 5) {

                    $tempo = (float) str_replace(",", ".", $dados[0]);
                    $x = (float) str_replace(",", ".", $dados[1]);
                    $y = (float) str_replace(",", ".", $dados[2]);
                    $z = (float) str_replace(",", ".", $dados[3]);
                    $total = (float) str_replace(",", ".", $dados[4]);

                    $stmt = $conn->prepare("
                        INSERT INTO leituras_sensores 
                        (tipo_sensor, tempo, eixo_x, eixo_y, eixo_z, total)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");

                    if (!$stmt) {
                        die("Erro no prepare: " . $conn->error);
                    }

                    $stmt->bind_param("sddddd", $tipo_sensor, $tempo, $x, $y, $z, $total);

                    if (!$stmt->execute()) {
                        die("Erro ao inserir: " . $stmt->error);
                    }

                    $linhas_importadas++;
                }
            }

            fclose($handle);

            header("Location: resultados.php?tipo_sensor=" . urlencode($tipo_sensor) . "&sucesso=1&linhas=" . $linhas_importadas);
exit;

        } else {
            echo "Erro ao abrir o arquivo CSV.";
        }

    } else {
        echo "Erro no envio do arquivo.";
    }

} else {
    echo "Acesse esta página pelo formulário de upload.";
}
?>