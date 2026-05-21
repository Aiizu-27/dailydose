<?php
// Carga el .env manualmente
$lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;
    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

$host      = $_ENV['DB_HOST'];
$usuario   = $_ENV['DB_USER'];
$contrasena = $_ENV['DB_PASS'];
$basedatos = $_ENV['DB_NAME'];

ini_set('display_errors', 0); // 0 en producción
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $usuario, $contrasena, $basedatos);
    $conn->set_charset("utf8");
} catch (Exception $e) {
    error_log($e->getMessage());
    die(json_encode(["status" => "error_servidor"]));
}