<?php
session_start();
require_once "../includes/config.php";

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

$nombre    = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$correo    = trim($_POST['correo'] ?? '');
$pass      = $_POST['contrasena'] ?? '';
$telefono  = trim($_POST['telefono'] ?? '');

// ===== VALIDACIONES =====
if (empty($nombre) || empty($correo) || empty($pass)) {
    echo json_encode(["status" => "campos_vacios"]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "correo_invalido"]);
    exit;
}

if (strlen($pass) < 8) {
    echo json_encode(["status" => "contrasena_corta"]);
    exit;
}

if (!preg_match('/[A-Z]/', $pass)) {
    echo json_encode(["status" => "contrasena_sin_mayuscula"]);
    exit;
}

if (!preg_match('/[a-z]/', $pass)) {
    echo json_encode(["status" => "contrasena_sin_minuscula"]);
    exit;
}

if (!preg_match('/[0-9]/', $pass)) {
    echo json_encode(["status" => "contrasena_sin_numero"]);
    exit;
}

if (!preg_match('/[\W_]/', $pass)) {
    echo json_encode(["status" => "contrasena_sin_simbolo"]);
    exit;
}

// ===== CORREO DUPLICADO =====
$stmt = $conn->prepare("SELECT ID_USUARIO FROM USUARIOS WHERE EMAIL = ?");
if (!$stmt) {
    error_log("Error prepare registro: " . $conn->error);
    echo json_encode(["status" => "error_servidor"]);
    exit;
}
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "correo_existente"]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// ===== HASH CONTRASEÑA =====
$pass_hash = password_hash($pass, PASSWORD_DEFAULT);

// ===== LLAMADA AL SP =====
$stmt = $conn->prepare("CALL sp_registrar_cliente(?, ?, ?, ?, ?)");
if (!$stmt) {
    error_log("Error prepare SP registro: " . $conn->error);
    echo json_encode(["status" => "error_servidor"]);
    exit;
}

$stmt->bind_param("sssss", $nombre, $apellidos, $correo, $pass_hash, $telefono);

if ($stmt->execute()) {
    // Liberamos resultados del SP
    while ($stmt->more_results()) {
        $stmt->next_result();
    }
    echo json_encode(["status" => "registro_ok"]);
} else {
    error_log("Error SP registro: " . $stmt->error);
    echo json_encode(["status" => "error_servidor"]);
}

$stmt->close();
$conn->close();