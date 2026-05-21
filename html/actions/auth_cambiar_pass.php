<?php
session_start();
require_once "../includes/config.php";

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

// El ID siempre viene de la sesión, nunca del POST
if (!isset($_SESSION['ID_USUARIO'])) {
    echo json_encode(["status" => "no_autenticado"]);
    exit;
}

$id_usuario  = $_SESSION['ID_USUARIO'];
$pass_actual = $_POST['contrasena_actual'] ?? '';
$nueva_pass  = $_POST['nueva_contrasena'] ?? '';
$confirmar   = $_POST['confirmar_contrasena'] ?? '';

// ===== VALIDACIONES =====
if (empty($pass_actual) || empty($nueva_pass) || empty($confirmar)) {
    echo json_encode(["status" => "campos_vacios"]);
    exit;
}

if ($nueva_pass !== $confirmar) {
    echo json_encode(["status" => "contrasenas_no_coinciden"]);
    exit;
}

if ($pass_actual === $nueva_pass) {
    echo json_encode(["status" => "contrasena_igual_anterior"]);
    exit;
}

if (strlen($nueva_pass) < 8) {
    echo json_encode(["status" => "contrasena_corta"]);
    exit;
}

if (!preg_match('/[A-Z]/', $nueva_pass)) {
    echo json_encode(["status" => "contrasena_sin_mayuscula"]);
    exit;
}

if (!preg_match('/[a-z]/', $nueva_pass)) {
    echo json_encode(["status" => "contrasena_sin_minuscula"]);
    exit;
}

if (!preg_match('/[0-9]/', $nueva_pass)) {
    echo json_encode(["status" => "contrasena_sin_numero"]);
    exit;
}

if (!preg_match('/[\W_]/', $nueva_pass)) {
    echo json_encode(["status" => "contrasena_sin_simbolo"]);
    exit;
}

// ===== VERIFICAR CONTRASEÑA ACTUAL =====
$stmt = $conn->prepare("SELECT CONTRASENA FROM USUARIOS WHERE ID_USUARIO = ?");
if (!$stmt) {
    error_log("Error prepare verificar_pass: " . $conn->error);
    echo json_encode(["status" => "error_servidor"]);
    exit;
}
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario   = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario || !password_verify($pass_actual, $usuario['CONTRASENA'])) {
    echo json_encode(["status" => "contrasena_actual_incorrecta"]);
    exit;
}

// ===== LLAMADA AL SP =====
$pass_hash = password_hash($nueva_pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("CALL sp_cambiar_password(?, ?)");
if (!$stmt) {
    error_log("Error prepare SP cambiar_pass: " . $conn->error);
    echo json_encode(["status" => "error_servidor"]);
    exit;
}
$stmt->bind_param("is", $id_usuario, $pass_hash);
$stmt->execute();

$resultado = $stmt->get_result();
$fila      = $resultado->fetch_assoc();

// Liberamos resultados del SP
while ($stmt->more_results()) {
    $stmt->next_result();
}
$stmt->close();
$conn->close();

if ($fila && $fila['filas_afectadas'] > 0) {
    $_SESSION['CAMBIAR_PASSWORD'] = false;
    echo json_encode(["status" => "cambio_ok"]);
} else {
    echo json_encode(["status" => "error_servidor"]);
}