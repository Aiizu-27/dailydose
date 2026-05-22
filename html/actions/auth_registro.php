<?php
session_start();
require_once "../includes/config.php";
header('Content-Type: application/json');

$nombre    = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$correo    = trim($_POST['correo'] ?? '');
$pass      = $_POST['contrasena'] ?? '';
$telefono  = trim($_POST['telefono'] ?? '');

if (!empty($nombre) && !empty($correo) && !empty($pass)) {
    
    // LLAMADA AL PL: Verificamos si el correo ya está duplicado de forma segura
    $stmt_check = $conn->prepare("CALL sp_auth_verificar_email(?)");
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $existe = $stmt_check->get_result()->fetch_assoc();
    
    while ($conn->more_results()) $conn->next_result(); // Limpiar canal
    $stmt_check->close();

    if ($existe) {
        echo json_encode(["status" => "email_duplicado"]);
        exit();
    }

    // Procedes a registrar al usuario (aquí mantienes tu CALL sp_registrar_usuario existente)
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO USUARIOS (NOMBRE, APELLIDOS, EMAIL, CONTRASENA, ROL) VALUES (?, ?, ?, ?, 'CLIENTE')");
    $stmt->bind_param("ssss", $nombre, $apellidos, $correo, $pass_hash);
    
    if ($stmt->execute()) {
        $id_usuario = $conn->insert_id;
        $stmt->close();
        
        // Crear el registro en la tabla clientes
        $stmt_c = $conn->prepare("INSERT INTO CLIENTES (ID_USUARIO, TELEFONO, PUNTOS) VALUES (?, ?, 0)");
        $stmt_c->bind_param("is", $id_usuario, $telefono);
        $stmt_c->execute();
        $stmt_c->close();

        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error_bd"]);
    }
} else {
    echo json_encode(["status" => "campos_vacios"]);
}
$conn->close();