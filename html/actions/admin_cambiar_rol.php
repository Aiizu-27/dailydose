<?php
session_start();
require_once "../includes/config.php";
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') {
    echo json_encode(["status" => "sin_permiso"]); exit();
}

$id_usuario = intval($_POST['id_usuario'] ?? 0);
$nuevo_rol  = $_POST['nuevo_rol'] ?? '';

if ($id_usuario > 0 && in_array($nuevo_rol, ['CLIENTE', 'EMPLEADO', 'ADMIN'])) {
    $stmt = $conn->prepare("UPDATE USUARIOS SET ROL = ? WHERE ID_USUARIO = ?");
    $stmt->bind_param("si", $nuevo_rol, $id_usuario);
    if ($stmt->execute()) { echo json_encode(["status" => "ok"]); } 
    else { echo json_encode(["status" => "error_bd"]); }
    $stmt->close();
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();