<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Sistema de Sensores</h1>
    <p>Importación de datos CSV generados por el Physics Toolbox Suite</p>

    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <label>Tipo de sensor:</label>
        <select name="tipo_sensor" required>
            <option value="">Selecione</option>
            <option value="Magnetometro">Magnetômetro</option>
            <option value="Giroscopio">Giroscópio</option>
            <option value="Acelerometro">Acelerômetro</option>
            <option value="Acelerometro">Motaña Rusa</option>
        </select>

        <label>Arquivo CSV:</label>
        <input type="file" name="arquivo_csv" accept=".csv" required>

        <button type="submit">Importar CSV</button>
    </form>

    <a href="resultados.php">Ver resultados</a>
    <a href="gerenciar.php">Gerenciar dados</a>
    <a href="dados.php">Visualizar dados armazenados</a>
    <a href="graficos.php">Ver gráficos dos sensores</a>
</div>

</body>
</html>