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
$dias_trabajo  = array_map('intval', $_POST['dias'] ?? []); // offsets 0..6 desde fecha_inicio (días que SÍ trabaja)

$dt_inicio = DateTime::createFromFormat('Y-m-d', $fecha_inicio);

if ($id_empleado > 0 && $dt_inicio !== false && in_array($turno, ['MAÑANA', 'TARDE'])) {
    $stmt_upsert = $conn->prepare("INSERT INTO TURNOS (ID_EMPLEADO, DIA, TURNO) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE TURNO = ?");
    $stmt_upsert->bind_param("isss", $id_empleado, $dia, $turno, $turno);

    $stmt_borrar = $conn->prepare("DELETE FROM TURNOS WHERE ID_EMPLEADO = ? AND DIA = ?");
    $stmt_borrar->bind_param("is", $id_empleado, $dia);

    $ok = true;
    for ($i = 0; $i < 7; $i++) {
        $dia = (clone $dt_inicio)->modify("+$i day")->format('Y-m-d');
        $stmt = in_array($i, $dias_trabajo, true) ? $stmt_upsert : $stmt_borrar;
        if (!$stmt->execute()) { $ok = false; break; }
    }

    $stmt_upsert->close();
    $stmt_borrar->close();
    echo json_encode(["status" => $ok ? "ok" : "error_bd"]);
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();
