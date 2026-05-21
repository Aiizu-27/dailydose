<?php
session_start();
require_once "../includes/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../carta.php");
    exit();
}

$id_producto = intval($_POST['id_producto'] ?? 0);

if ($id_producto <= 0) {
    header("Location: ../carta.php");
    exit();
}

// ===== CONSULTAMOS DATOS DEL PRODUCTO DESDE LA BD =====
$stmt = $conn->prepare("CALL sp_obtener_producto_carrito(?)");
if (!$stmt) {
    error_log("Error prepare add_carrito: " . $conn->error);
    header("Location: ../carta.php");
    exit();
}
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();
$producto  = $resultado->fetch_assoc();

while ($stmt->more_results()) {
    $stmt->next_result();
}
$stmt->close();
$conn->close();

if (!$producto) {
    header("Location: ../carta.php");
    exit();
}

// ===== CARRITO EN SESIÓN =====
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$cantidad_actual = isset($_SESSION['carrito'][$id_producto])
    ? $_SESSION['carrito'][$id_producto]['cantidad']
    : 0;

if (($cantidad_actual + 1) <= $producto['STOCK']) {
    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]['cantidad']++;
    } else {
        $_SESSION['carrito'][$id_producto] = [
            'nombre'   => $producto['NOMBRE'],
            'precio'   => $producto['PRECIO'],
            'cantidad' => 1
        ];
    }
} else {
    $_SESSION['error_carrito'] = "Has alcanzado el límite de stock de " . $producto['NOMBRE'];
}

header("Location: ../carta.php");
exit();