<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

if (isset($_SESSION['ID_USUARIO'])) {
    echo json_encode(["status" => "ya_autenticado", "rol" => $_SESSION['ROL']]);
    exit;
}

$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$pass   = $_POST['contrasena'] ?? '';

if (empty($correo) || empty($pass)) {
    echo json_encode(["status" => "campos_vacios"]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "correo_invalido"]);
    exit;
}

$stmt = $conn->prepare("CALL sp_obtener_usuario_login(?)");
if (!$stmt) {
    error_log("Error prepare login: " . $conn->error);
    echo json_encode(["status" => "error_servidor"]);
    exit;
}

$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($usuario = $resultado->fetch_assoc()) {
    if (password_verify($pass, $usuario['CONTRASENA'])) {

        session_regenerate_id(true);

        $_SESSION['ID_USUARIO'] = $usuario['ID_USUARIO'];
        $_SESSION['NOMBRE']     = $usuario['NOMBRE'];
        $_SESSION['ROL']        = $usuario['ROL'];

        if ($usuario['CAMBIAR_PASSWORD']) {
            $response = ["status" => "cambiar_password"];
        } else {
            $response = ["status" => "login_ok", "rol" => $usuario['ROL']];
        }

    } else {
        $response = ["status" => "contrasena_incorrecta"];
    }
} else {
    $response = ["status" => "usuario_no_encontrado"];
}

while ($stmt->more_results()) {
    $stmt->next_result();
}

$stmt->close();
$conn->close();

echo json_encode($response);
exit;