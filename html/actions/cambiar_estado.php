<?php
session_start();
require_once "../includes/config.php";

// Reporte de errores activado
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id_pedido = intval($_POST['id']);
    $nuevo_estado = $_POST['estado'];
    $id_empleado = !empty($_POST['id_empleado']) ? intval($_POST['id_empleado']) : null;

    try {
        // Aseguramos que el autocommit esté activado para que guarde al momento
        $conn->autocommit(TRUE);

        if ($nuevo_estado == 'EN PREPARACION' && $id_empleado) {
            // Caso 1: Pasamos a preparación y asignamos empleado
            $sql = "UPDATE PEDIDOS SET ESTADO = ?, ID_EMPLEADO = ? WHERE ID_PEDIDO = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $nuevo_estado, $id_empleado, $id_pedido);
        } else {
            // Caso 2: Otros cambios de estado
            $sql = "UPDATE PEDIDOS SET ESTADO = ? WHERE ID_PEDIDO = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nuevo_estado, $id_pedido);
        }

        if ($stmt->execute()) {
            // FORZAMOS EL GUARDADO FÍSICO
            $conn->query("COMMIT"); 
            $stmt->close();
            
            // En lugar de mostrar pantalla blanca, redirigimos directamente
            // para que veas el cambio al instante
            header("Location: ../panel/dashboard_trabajador.php?status=success");
            exit();
        }

    } catch (Exception $e) {
        die("Error en la BD: " . $e->getMessage());
    }
} else {
    header("Location: ../panel/dashboard_trabajador.php");
    exit();
}