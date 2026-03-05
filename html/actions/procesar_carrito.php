<?php
session_start();
require_once "../includes/config.php";

// 1. SEGURIDAD: Si no hay sesión o el carrito está vacío, fuera.
if (!isset($_SESSION['ID_USUARIO']) || empty($_SESSION['carrito'])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['ID_USUARIO'];
$numero_mesa = !empty($_POST['numero_mesa']) ? intval($_POST['numero_mesa']) : null;
$total_pedido = 0;
$fecha_actual = date("Y-m-d");

// A. Necesitamos el ID_CLIENTE (que es distinto al ID_USUARIO)
$stmt_cli = $conn->prepare("SELECT ID_CLIENTE FROM CLIENTES WHERE ID_USUARIO = ?");
$stmt_cli->bind_param("i", $id_usuario);
$stmt_cli->execute();
$res_cli = $stmt_cli->get_result();
$cliente = $res_cli->fetch_assoc();
$id_cliente = $cliente['ID_CLIENTE'];

// B. Calculamos el total
foreach ($_SESSION['carrito'] as $item) {
    $total_pedido += ($item['precio'] * $item['cantidad']);
}

// C. Calculamos puntos (1€ = 1 punto)
$puntos_ganados = floor($total_pedido);

// INICIAMOS TRANSACCIÓN
$conn->begin_transaction();

try {
    // 2. INSERTAR EN TABLA PEDIDOS
    // Nota: Dejamos ID_EMPLEADO como NULL porque aún no lo ha aceptado nadie
    $stmt = $conn->prepare("INSERT INTO PEDIDOS (FECHA, TOTAL, ID_CLIENTE, NUMERO_MESA, ESTADO) VALUES (?, ?, ?, ?, 'PENDIENTE')");
    $stmt->bind_param("sdii", $fecha_actual, $total_pedido, $id_cliente, $numero_mesa);
    $stmt->execute();
    $id_pedido = $conn->insert_id;

    // 3. INSERTAR EN TABLA DETALLE_PEDIDO Y ACTUALIZAR STOCK
    $stmt_det = $conn->prepare("INSERT INTO DETALLE_PEDIDO (ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL) VALUES (?, ?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE PRODUCTOS SET STOCK = STOCK - ? WHERE ID_PRODUCTO = ?");

    foreach ($_SESSION['carrito'] as $id_prod => $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        
        // Insertar detalle
        $stmt_det->bind_param("iiidd", $id_pedido, $id_prod, $item['cantidad'], $item['precio'], $subtotal);
        $stmt_det->execute();

        // Restar stock
        $stmt_stock->bind_param("ii", $item['cantidad'], $id_prod);
        $stmt_stock->execute();
    }

    // 4. ACTUALIZAR PUNTOS DEL CLIENTE
    $stmt_pts = $conn->prepare("UPDATE CLIENTES SET PUNTOS = PUNTOS + ? WHERE ID_CLIENTE = ?");
    $stmt_pts->bind_param("ii", $puntos_ganados, $id_cliente);
    $stmt_pts->execute();

    // TODO OK: Confirmamos cambios
    $conn->commit();

    // Limpiamos carrito
    unset($_SESSION['carrito']);

    header("Location: ../index.php?pedido=ok&puntos=" . $puntos_ganados);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error crítico: " . $e->getMessage());
}