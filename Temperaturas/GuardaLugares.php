<?php
date_default_timezone_set("America/Hermosillo");

// Leer datos via POST y definir los parametros owo 
$Lug = $_POST["lug"];


echo "<h1>Fecha del dia de hoy:</h1>";
echo "<br>" . $Ubi;
echo "<br>" . $temp;
echo "<br>" . date("Y-m-d");
echo "<br>" . date("H:i");
echo "<p>datos provenientes de formulario.php</p>";

$fecha = date("Y-m-d H:i:s");
//Configuracion de red 
$host    = 'localhost';
$db      = 'temperaturaejem';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [    
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $si = 0;

    //Busqueda de emau y tido
    $stmt = $pdo->prepare("SELECT idubicaciones FROM ubicaciones WHERE ubucaciones =? ");
    $stmt->execute([$Lug]);
    while ($row = $stmt->fetch())
        {
            echo "El lugar Ya existe en la base de datos";
            exit;
        }               
        $sql  = "INSERT INTO ubicaciones(ubucaciones) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$Lug]);


} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>