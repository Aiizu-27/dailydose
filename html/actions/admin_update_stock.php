<?php
session_start();
require_once "../includes/config.php";
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') { 
    echo json_encode(["status" => "sin_permiso"]); exit(); 
}

$id_producto = intval($_POST['id_producto'] ?? 0);
$nuevo_stock = intval($_POST['nuevo_stock'] ?? 0);

if ($id_producto > 0 && $nuevo_stock >= 0) {
    $conn->begin_transaction();
    try {
        // Calcular la diferencia de unidades
        $stmt = $conn->prepare("SELECT STOCK FROM PRODUCTOS WHERE ID_PRODUCTO = ?");
        $stmt->bind_param("i", $id_producto); $stmt->execute();
        $prod = $stmt->get_result()->fetch_assoc(); $stmt->close();

        if (!$prod) { echo json_encode(["status" => "no_existe"]); exit(); }
        $diferencia = $nuevo_stock - $prod['STOCK'];

        // 1. Modificar stock
        $stmt = $conn->prepare("UPDATE PRODUCTOS SET STOCK = ? WHERE ID_PRODUCTO = ?");
        $stmt->bind_param("ii", $nuevo_stock, $id_producto); $stmt->execute(); $stmt->close();

        // 2. Registrar movimiento en la bitácora
        $tipo = 'AJUSTE';
        $stmt = $conn->prepare("INSERT INTO INVENTARIO (ID_PRODUCTO, CANTIDAD, TIPO_MOVIMIENTO) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_producto, $diferencia, $tipo); $stmt->execute(); $stmt->close();

        $conn->commit();
        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error_inventario"]);
    }
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();