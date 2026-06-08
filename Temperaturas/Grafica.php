<?php
include("conexion.php");
/** @var mysqli $conexion */

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$idUbicacion = intval($_GET['id']);

// Traemos todas las temperaturas de la ubicación seleccionada
$query = "
    SELECT
        t.tempfechahora,
        t.temptemperatura,
        l.lugnombre AS Ubicacion
    FROM temperatura t
    JOIN lugar l ON t.lugar_idlugar = l.idlugar
    WHERE t.lugar_idlugar = $idUbicacion
    ORDER BY t.tempfechahora ASC
";
$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

// Nombre de la ubicación
$queryNombre = "SELECT lugnombre FROM lugar WHERE idlugar = $idUbicacion";
$resNombre   = mysqli_query($conexion, $queryNombre);
$filaNombre  = mysqli_fetch_assoc($resNombre);
$nombreUbicacion = $filaNombre ? $filaNombre['lugnombre'] : "Ubicación desconocida";

// Construimos los arreglos para Chart.js
$etiquetas    = [];
$temperaturas = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $etiquetas[]    = $fila['tempfechahora'];
    $temperaturas[] = $fila['temptemperatura'];
}

$etiquetas_json    = json_encode($etiquetas);
$temperaturas_json = json_encode($temperaturas);
$nombreUbicacion_json = json_encode($nombreUbicacion);

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica — <?php echo htmlspecialchars($nombreUbicacion); ?></title>
    <link rel="stylesheet" href="grafica.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Gráfica de Temperatura</h1>
<p class="subtitulo">
    Ubicación seleccionada: <strong><?php echo htmlspecialchars($nombreUbicacion); ?></strong>
    &nbsp;|&nbsp; ID: <?php echo $idUbicacion; ?>
</p>

<div class="caja-grafica">
    <h2>Temperatura registrada en: <?php echo htmlspecialchars($nombreUbicacion); ?></h2>
    <?php if (empty($etiquetas)): ?>
        <p style="color:#999; text-align:center; padding:30px;">
            No hay registros de temperatura para esta ubicación.
        </p>
    <?php else: ?>
        <canvas id="miGrafica"></canvas>
    <?php endif; ?>
</div>

<div class="contenedor-boton">
    <a href="index.php" class="boton-regresar">← Regresar</a>
</div>

<footer>
    Base de datos: temperaturaejem | Desarrollado por: Sombra117 | &copy; 2026
</footer>

<?php if (!empty($etiquetas)): ?>
<script>
    const etiquetas    = <?php echo $etiquetas_json; ?>;
    const temperaturas = <?php echo $temperaturas_json; ?>;

    const ctx = document.getElementById('miGrafica').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperaturas,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#2980b9'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Registro de temperatura — ' + <?php echo $nombreUbicacion_json; ?>
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Fecha y Hora' },
                    ticks: { maxRotation: 45, minRotation: 45 }
                },
                y: {
                    title: { display: true, text: 'Temperatura (°C)' },
                    beginAtZero: false
                }
            }
        }
    });
</script>
<?php endif; ?>

</body>
</html>