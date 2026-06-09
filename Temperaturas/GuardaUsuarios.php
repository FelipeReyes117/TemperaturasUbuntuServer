<?php

header('Content-Type: application/json; charset=utf-8');

function responder(string $status, string $mensaje, array $extra = []): void {
    
    echo json_encode(
        array_merge(['status' => $status, 'mensaje' => $mensaje], $extra),
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder('error', 'Método no permitido.');
}


$cor = trim($_POST['cor'] ?? '');
$nom = trim($_POST['nom'] ?? '');
if (empty($cor) || empty($nom)) {
    responder('error', 'Todos los campos son obligatorios.');
}
if (!filter_var($cor, FILTER_VALIDATE_EMAIL)) {
    responder('error', 'El formato del correo no es válido.');
}
//COnfi red
$host    = 'localhost';
$db      = 'temperaturas23060301';
$user    = 'user23060301';
$pass    = 'pekaelectrico';
$charset = 'utf8mb4';  // [11] utf8mb4 soporta emojis y todos los Unicode
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

//   FETCH_ASSOC       → los resultados vienen como array asociativo ['columna' => valor]
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE email = ?");
    $stmt->execute([$cor]);
    if ($row = $stmt->fetch()) {
        responder('warn', "El correo ya está registrado a nombre de: {$row['nombre']}");
    }

    $passGenerado = bin2hex(random_bytes(16));
    $sql  = "INSERT INTO usuarios (email, password, nombre) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cor, $passGenerado, $nom]);


    responder('success', "¡Usuario '{$nom}' registrado correctamente!", [
        'password' => $passGenerado
    ]);


// ─────────────────────────────────────────────────────────────
} catch (PDOException $e) {
    responder('error', 'Error de base de datos: ' . $e->getMessage());
}
?>