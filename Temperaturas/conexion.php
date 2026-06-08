<?php
define('DB_HOST',     'localhost');
define('DB_USER',     'user23060301');
define('DB_PASSWORD', 'pekaelectrico');
define('DB_NAME',     'user23060301');
define('DB_CHARSET',  'utf8mb4');

function getConexion(): mysqli {
    $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_set_charset($conexion, DB_CHARSET);
    return $conexion;
}
$conexion = getConexion();