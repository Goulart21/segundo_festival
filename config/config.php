
<?php

$envPath = __DIR__ . '/../.env';

$env = parse_ini_file(
    $envPath,
    false,
    INI_SCANNER_RAW
);

if ($env === false) {
    die('Erro ao carregar o arquivo');
}

$localhost = $env['DB_HOST'];
$user = $env['DB_USER'];
$dbname = $env['DB_NAME'];
$password = $env['DB_PASSWORD'];

try {

    $pdo = new PDO(
        "mysql:host=$localhost;dbname=$dbname",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ERRMODE
    );

    echo 'conectado';
} catch (PDOException $e) {

    echo 'Erro ao conectar';
    $e->getMessage();
}
