<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Sensores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Sistema de Sensores</h1>
    <p>Importación de datos CSV generados por Physics Toolbox Suite</p>

    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <label>Tipo de sensor:</label>
        <select name="tipo_sensor" required>
            <option value="">Seleccione</option>
            <option value="Magnetómetro">Magnetómetro</option>
            <option value="Giroscopio">Giroscopio</option>
            <option value="Acelerómetro">Acelerómetro</option>
            <option value="Montaña Rusa">Montaña rusa</option>
        </select>

        <label>Archivo CSV:</label>
        <input type="file" name="arquivo_csv" accept=".csv" required>

        <button type="submit">Importar CSV</button>
    </form>

    <div class="menu-botones">
        <a href="resultados.php" class="boton-menu">Ver resultados</a>
        <a href="gerenciar.php" class="boton-menu">Gestionar datos</a>
        <a href="dados.php" class="boton-menu">Visualizar datos almacenados</a>
        <a href="graficos.php" class="boton-menu">Ver gráficos de los sensores</a>
    </div>
</div>

</body>
</html>