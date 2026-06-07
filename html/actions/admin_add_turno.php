<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') {
    echo json_encode(["status" => "sin_permiso"]); exit();
}

$id_empleado   = intval($_POST['id_empleado'] ?? 0);
$fecha_inicio  = $_POST['fecha_inicio'] ?? '';
$turno         = $_POST['bloque_turno'] ?? '';

$dt_inicio = DateTime::createFromFormat('Y-m-d', $fecha_inicio);

if ($id_empleado > 0 && $dt_inicio !== false && in_array($turno, ['MAÑANA', 'TARDE'])) {
    $stmt = $conn->prepare("INSERT INTO TURNOS (ID_EMPLEADO, DIA, TURNO) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE TURNO = ?");
    $stmt->bind_param("isss", $id_empleado, $dia, $turno, $turno);

    $ok = true;
    for ($i = 0; $i < 7; $i++) {
        $dia = $dt_inicio->modify($i === 0 ? '+0 day' : '+1 day')->format('Y-m-d');
        if (!$stmt->execute()) { $ok = false; break; }
    }

    $stmt->close();
    echo json_encode(["status" => $ok ? "ok" : "error_bd"]);
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();
