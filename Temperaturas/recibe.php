<?php
date_default_timezone_set("America/Hermosillo");

ini_set('display_errors', 1);
error_reporting(E_ALL);


$temp  = $_POST["temp"]  ?? null;
$Ubi   = $_POST["Ubi"]   ?? null;
$cor   = $_POST["cor"]   ?? null;
$passw = $_POST["pass"]  ?? null;


if (is_null($temp) || is_null($Ubi) || is_null($cor) || is_null($passw)) {
    http_response_code(400);
    die("Faltan datos requeridos.");
}

$fecha = date("Y-m-d H:i:s");

// ── Conexión PDO ─────────────────────────────────────────────
$host    = 'localhost';
$db      = 'temperaturas23060301';
$user    = 'user23060301';
$pass    = 'pekaelectrico';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    
    $stmtAuth = $pdo->prepare("
        SELECT id FROM usuarios
        WHERE email = ? AND password = ?
        LIMIT 1
    ");
    $stmtAuth->execute([$cor, $passw]);


    $usuario = $stmtAuth->fetch();
    if (!$usuario) {
        http_response_code(401);
        echo "CREDENCIALES INVÁLIDAS";
        exit;
    }

    // Verificar que la ubicación existe
    $stmtUbi = $pdo->prepare("
        SELECT idlugar FROM lugar
        WHERE idlugar = ?
        LIMIT 1
    ");
    $stmtUbi->execute([$Ubi]);


    $ubicacion = $stmtUbi->fetch();
    if (!$ubicacion) {
        http_response_code(404);
        echo "UBICACIÓN NO REGISTRADA EN LA BASE DE DATOS";
        exit;
    }

    // Insertar la temperatura
    $stmtInsert = $pdo->prepare("
        INSERT INTO temperatura (temptemperatura, tempfechahora, lugar_idlugar)
        VALUES (?, ?, ?)
    ");
    $stmtInsert->execute([$temp, $fecha, $Ubi]);

    http_response_code(200);
    echo "OK: Temperatura $temp °C registrada en ubicacion $Ubi el $fecha";

} catch (\PDOException $e) {
    http_response_code(500);
   
    echo "Error de base de datos: " . $e->getMessage();
}
?>