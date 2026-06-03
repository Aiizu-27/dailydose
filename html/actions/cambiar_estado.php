<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['ID_USUARIO'])) {
    echo json_encode(["status" => "no_autenticado"]);
    exit;
}

if (!in_array($_SESSION['ROL'], ['EMPLEADO', 'ADMIN'])) {
    echo json_encode(["status" => "sin_permiso"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(["status" => "peticion_invalida"]);
    exit;
}

$id_pedido    = intval($_POST['id']);
$nuevo_estado = $_POST['estado'] ?? '';
$id_empleado  = !empty($_POST['id_empleado']) ? intval($_POST['id_empleado']) : null;

$estados_validos = ['PENDIENTE', 'EN_PREPARACION', 'LISTO', 'ENTREGADO', 'CANCELADO'];
if (!in_array($nuevo_estado, $estados_validos)) {
    echo json_encode(["status" => "estado_invalido"]);
    exit;
}

if ($id_pedido <= 0) {
    echo json_encode(["status" => "pedido_invalido"]);
    exit;
}

$stmt = $conn->prepare("CALL sp_cambiar_estado_pedido(?, ?, ?)");
if (!$stmt) {
    error_log("Error prepare cambiar_estado: " . $conn->error);
    echo json_encode(["status" => "error_servidor"]);
    exit;
}

$stmt->bind_param("isi", $id_pedido, $nuevo_estado, $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$fila      = $resultado->fetch_assoc();

while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
$conn->close();

if ($fila && $fila['filas_afectadas'] > 0) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "pedido_no_encontrado"]);
}