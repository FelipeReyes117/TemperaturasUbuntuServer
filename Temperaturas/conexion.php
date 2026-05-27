<?php

// ── ⚙️  CAMBIA ESTOS VALORES según tu servidor ──────────────
define('DB_HOST',     'localhost');
define('DB_USER',     'root');           // ← Tu usuario de MySQL
define('DB_PASSWORD', '');               // ← Tu contraseña (vacía en XAMPP local)
define('DB_NAME',     'temperaturaejem'); // Nombre de la base de datos
define('DB_CHARSET',  'utf8mb4');
// ─────────────────────────────────────────────────────────────

/**
 * Devuelve la conexión activa a MySQL.
 * @return mysqli
 */
function getConexion(): mysqli {
    $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$conexion) {
        die("❌ Error de conexión: " . mysqli_connect_error());
    }

    mysqli_set_charset($conexion, DB_CHARSET);
    return $conexion;
}

// Variable global lista para usar con include()
$conexion = getConexion();