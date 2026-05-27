<?php
include("conexion.php");
/** @var mysqli $conexion */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de temperaturas</title>
    <link rel="stylesheet" href="Styles.css">
</head>
<body>

<h1>Monitoreo de temperaturas</h1>

<div class="contenedor-tablas">

    <!-- TABLA 1: Todas las ubicaciones con su última temperatura -->
    <div class="caja-tabla">
        <h2>Tabla: ubicaciones</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ubicación</th>
                    <th>Última Temperatura (°C)</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
            <?php
                // Traemos ubicaciones junto con su temperatura más reciente
                $query = "
                    SELECT
                        u.idubicaciones,
                        u.ubucaciones,
                        t.temperaturas   AS ultima_temp,
                        t.temperaturasfecha AS ultima_fecha
                    FROM ubicaciones u
                    LEFT JOIN temperaturas t
                        ON t.idtemperaturas = (
                            SELECT idtemperaturas
                            FROM temperaturas
                            WHERE ubicaciones_idubicaciones = u.idubicaciones
                            ORDER BY temperaturasfecha DESC
                            LIMIT 1
                        )
                    ORDER BY u.idubicaciones ASC
                ";
                $resultado = mysqli_query($conexion, $query);

                if (!$resultado) {
                    die("Error en la consulta: " . mysqli_error($conexion));
                }

                if (mysqli_num_rows($resultado) > 0) {
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        $id        = $fila['idubicaciones'];
                        $ubicacion = htmlspecialchars($fila['ubucaciones']);
                        $temp      = $fila['ultima_temp'] !== null
                                        ? htmlspecialchars($fila['ultima_temp']) . ' °C'
                                        : '<em style="color:#aaa;">Sin registros</em>';
                        $fecha     = $fila['ultima_fecha'] !== null
                                        ? htmlspecialchars($fila['ultima_fecha'])
                                        : '—';

                        echo "<tr onclick=\"seleccionarFila(this, $id)\" style=\"cursor:pointer\">";
                        echo "<td>$id</td>";
                        echo "<td>$ubicacion</td>";
                        echo "<td>$temp</td>";
                        echo "<td>$fecha</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='vacio'>No hay registros en la tabla.</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

    <!-- TABLA 2: Solo ID y Nombre de ubicación -->
    <div class="caja-tabla">
        <h2>Nombre — ubicaciones</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $query2 = "SELECT idubicaciones, ubucaciones FROM ubicaciones ORDER BY idubicaciones ASC";
                $resultado2 = mysqli_query($conexion, $query2);

                if (!$resultado2) {
                    die("Error en la consulta 2: " . mysqli_error($conexion));
                }

                if (mysqli_num_rows($resultado2) > 0) {
                    while ($fila2 = mysqli_fetch_assoc($resultado2)) {
                        $id2  = $fila2['idubicaciones'];
                        $nom2 = htmlspecialchars($fila2['ubucaciones']);

                        echo "<tr onclick=\"seleccionarFila(this, $id2)\" style=\"cursor:pointer\">";
                        echo "<td>$id2</td>";
                        echo "<td>$nom2</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' class='vacio'>No hay registros en la tabla.</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

</div><!-- fin contenedor-tablas -->

<!-- Aviso de selección -->
<p id="aviso" style="text-align:center; color:#888; font-size:13px; margin-top:10px;">
    Haz clic en una fila para seleccionar una ubicación
</p>

<!-- Botón para ver la gráfica -->
<div class="contenedor-boton">
    <a href="#" id="boton-grafica" class="boton-grafica" onclick="return irAGrafica()">
        Ver gráfica de temperaturas
    </a>
</div>

<?php mysqli_close($conexion); ?>

<footer>
    Base de datos: temperaturaejem | Desarrollado por: Sombra117 | &copy; 2026
</footer>

<script>
    let idSeleccionado = null;

    function seleccionarFila(filaClickeada, id) {
        document.querySelectorAll('tr').forEach(function(fila) {
            fila.classList.remove('fila-seleccionada');
        });
        filaClickeada.classList.add('fila-seleccionada');
        idSeleccionado = id;
        document.getElementById('aviso').textContent = '✔ Ubicación seleccionada: ID ' + id;
        document.getElementById('aviso').style.color = '#27ae60';
    }

    function irAGrafica() {
        if (idSeleccionado === null) {
            alert('Por favor, selecciona una ubicación de la tabla primero.');
            return false;
        }
        window.location.href = 'grafica.php?id=' + idSeleccionado;
        return false;
    }
</script>

</body>
</html>