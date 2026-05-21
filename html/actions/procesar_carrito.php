<?php
session_start();
require_once "../includes/config.php";

if (!isset($_SESSION['ID_USUARIO']) || empty($_SESSION['carrito'])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario  = $_SESSION['ID_USUARIO'];
$id_mesa     = !empty($_POST['id_mesa']) ? intval($_POST['id_mesa']) : null;
$total_pedido = 0;

// ===== OBTENER ID_CLIENTE =====
$stmt = $conn->prepare("SELECT ID_CLIENTE FROM CLIENTES WHERE ID_USUARIO = ?");
if (!$stmt) {
    error_log("Error prepare get_cliente: " . $conn->error);
    header("Location: ../carrito.php?error=servidor");
    exit();
}
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$cliente    = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    header("Location: ../index.php");
    exit();
}
$id_cliente = $cliente['ID_CLIENTE'];

// ===== CALCULAR TOTAL Y PUNTOS =====
foreach ($_SESSION['carrito'] as $item) {
    $total_pedido += ($item['precio'] * $item['cantidad']);
}
$total_pedido  = round($total_pedido, 2);
$puntos_ganados = floor($total_pedido);

// ===== TRANSACCIÓN =====
$conn->begin_transaction();

try {
    // 1. Crear pedido y sumar puntos
    $stmt = $conn->prepare("CALL sp_procesar_pedido(?, ?, ?, ?)");
    if (!$stmt) throw new Exception("Error prepare sp_procesar_pedido: " . $conn->error);

    $stmt->bind_param("iidi", $id_cliente, $id_mesa, $total_pedido, $puntos_ganados);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila      = $resultado->fetch_assoc();
    $id_pedido = $fila['ID_PEDIDO'];

    while ($stmt->more_results()) $stmt->next_result();
    $stmt->close();

    // 2. Insertar detalle línea a línea
    $stmt_det = $conn->prepare("CALL sp_insertar_detalle_pedido(?, ?, ?, ?, ?)");
    if (!$stmt_det) throw new Exception("Error prepare sp_detalle: " . $conn->error);

    foreach ($_SESSION['carrito'] as $id_prod => $item) {
        $subtotal = round($item['precio'] * $item['cantidad'], 2);
        $stmt_det->bind_param("iiidd", $id_pedido, $id_prod, $item['cantidad'], $item['precio'], $subtotal);
        $stmt_det->execute();
        while ($stmt_det->more_results()) $stmt_det->next_result();
    }
    $stmt_det->close();

    $conn->commit();

    unset($_SESSION['carrito']);
    header("Location: ../index.php?pedido=ok&puntos=" . $puntos_ganados);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error procesar_carrito: " . $e->getMessage());
    header("Location: ../carrito.php?error=servidor");
    exit();
}