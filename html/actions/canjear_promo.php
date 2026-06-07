<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] !== 'CLIENTE') {
    header("Location: /login.php");
    exit;
}

$id_usuario = $_SESSION['ID_USUARIO'];
$id_recompensa = $_POST['id_promocion'] ?? null;

if (!$id_recompensa) {
    header("Location: /promociones.php?canje=error&motivo=datos_invalidos");
    exit;
}

$stmt = $conn->prepare("CALL sp_canjear_recompensa(?, ?)");
$stmt->bind_param("ii", $id_usuario, $id_recompensa);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();
while ($conn->more_results()) $conn->next_result();
$stmt->close();

$status = $row['status'] ?? 'error';
$motivo = $row['motivo'] ?? 'desconocido';

if ($status === 'ok') {
    $stmt_nombre = $conn->prepare("SELECT NOMBRE FROM RECOMPENSAS WHERE ID_RECOMPENSA = ?");
    $stmt_nombre->bind_param("i", $id_recompensa);
    $stmt_nombre->execute();
    $rec = $stmt_nombre->get_result()->fetch_assoc();
    $stmt_nombre->close();

    if ($rec) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        $clave = 'canje_' . $id_recompensa;
        $_SESSION['carrito'][$clave] = [
            'nombre'   => $rec['NOMBRE'] . ' (canje)',
            'precio'   => 0,
            'cantidad' => 1
        ];
    }
}

$conn->close();

header("Location: /promociones.php?canje=" . urlencode($status) . "&motivo=" . urlencode($motivo));
exit;
