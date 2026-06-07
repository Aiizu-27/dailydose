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
$conn->close();

$status = $row['status'] ?? 'error';
$motivo = $row['motivo'] ?? 'desconocido';

header("Location: /promociones.php?canje=" . urlencode($status) . "&motivo=" . urlencode($motivo));
exit;
