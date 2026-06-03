<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') { 
    echo json_encode(["status" => "sin_permiso"]); exit(); 
}

$id_empleado = intval($_POST['id_empleado'] ?? 0);
$fecha       = $_POST['fecha_turno'] ?? '';
$turno       = $_POST['bloque_turno'] ?? '';

if ($id_empleado > 0 && !empty($fecha) && in_array($turno, ['MAÑANA', 'TARDE'])) {
    $stmt = $conn->prepare("INSERT INTO TURNOS (ID_EMPLEADO, DIA, TURNO) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE TURNO = ?");
    $stmt->bind_param("isss", $id_empleado, $fecha, $turno, $turno);
    if ($stmt->execute()) { echo json_encode(["status" => "ok"]); } 
    else { echo json_encode(["status" => "error_bd"]); }
    $stmt->close();
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();