<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') {
    echo json_encode(["status" => "sin_permiso"]); exit();
}

$id_baja = intval($_POST['id_baja'] ?? 0);

if ($id_baja > 0) {
    $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE ID_USUARIO = ?");
    $stmt->bind_param("i", $id_baja);
    if ($stmt->execute()) { echo json_encode(["status" => "ok"]); } 
    else { echo json_encode(["status" => "error_bd"]); }
    $stmt->close();
} else { echo json_encode(["status" => "id_invalido"]); }
$conn->close();