<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') {
    echo json_encode(["status" => "sin_permiso"]); exit();
}

$nombre    = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email     = trim($_POST['email'] ?? '');
$puesto    = trim($_POST['puesto'] ?? 'BARISTA');
$salario   = floatval($_POST['salario'] ?? 0);
$pass      = $_POST['contrasena'] ?? '';

if (!empty($nombre) && !empty($email) && !empty($pass)) {
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    
    $conn->begin_transaction();
    try {
        // 1. Insertar en la tabla maestra de acceso
        $stmt = $conn->prepare("INSERT INTO USUARIOS (NOMBRE, APELLIDOS, EMAIL, CONTRASENA, ROL) VALUES (?, ?, ?, ?, 'EMPLEADO')");
        $stmt->bind_param("ssss", $nombre, $apellidos, $email, $pass_hash);
        $stmt->execute();
        $id_usuario = $conn->insert_id;
        $stmt->close();

        // 2. Insertar en la tabla relacional de Recursos Humanos
        $stmt_emp = $conn->prepare("INSERT INTO EMPLEADOS (ID_USUARIO, PUESTO, SALARIO, FECHA_CONTRATACION) VALUES (?, ?, ?, CURDATE())");
        $stmt_emp->bind_param("isd", $id_usuario, $puesto, $salario);
        $stmt_emp->execute();
        $stmt_emp->close();

        $conn->commit();
        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error_transaccion_personal"]);
    }
} else { echo json_encode(["status" => "datos_invalidos"]); }
$conn->close();