<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') { 
    echo json_encode(["status" => "sin_permiso"]); exit(); 
}

$nombre    = trim($_POST['nombre'] ?? '');
$categoria = strtoupper(trim($_POST['categoria'] ?? ''));
$precio    = floatval($_POST['precio'] ?? 0);
$stock     = intval($_POST['stock'] ?? 0);

if (!empty($nombre) && !empty($categoria) && $precio > 0) {
    $conn->begin_transaction();
    try {
        // Encontrar si ya existe la categoría
        $stmt = $conn->prepare("SELECT ID_CATEGORIA FROM CATEGORIAS WHERE UPPER(NOMBRE_CATEGORIA) = ?");
        $stmt->bind_param("s", $categoria); $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc(); $stmt->close();

        if ($res) { $id_cat = $res['ID_CATEGORIA']; } 
        else {
            // Si es nueva, la creamos al vuelo
            $stmt = $conn->prepare("INSERT INTO CATEGORIAS (NOMBRE_CATEGORIA) VALUES (?)");
            $stmt->bind_param("s", $categoria); $stmt->execute();
            $id_cat = $conn->insert_id; $stmt->close();
        }

        // Insertar producto real
        $stmt = $conn->prepare("INSERT INTO PRODUCTOS (NOMBRE, ID_CATEGORIA, PRECIO, STOCK) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sidi", $nombre, $id_cat, $precio, $stock); $stmt->execute(); $stmt->close();

        $conn->commit();
        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error_catalogo"]);
    }
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();