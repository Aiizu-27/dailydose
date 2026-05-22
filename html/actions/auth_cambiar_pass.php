<?php
session_start();
require_once "../includes/config.php";
header('Content-Type: application/json');

if (!isset($_SESSION['ID_USUARIO'])) {
    echo json_encode(["status" => "no_auth"]); exit();
}

$id_usuario = $_SESSION['ID_USUARIO'];
$pass_actual = $_POST['pass_actual'] ?? '';
$pass_nueva  = $_POST['pass_nueva'] ?? '';

if (!empty($pass_actual) && !empty($pass_nueva)) {

    // LLAMADA AL PL: Traemos el hash almacenado de la contraseña
    $stmt_hash = $conn->prepare("CALL sp_auth_obtener_password_hash(?)");
    $stmt_hash->bind_param("i", $id_usuario);
    $stmt_hash->execute();
    $usuario = $stmt_hash->get_result()->fetch_assoc();
    
    while ($conn->more_results()) $conn->next_result(); // Limpiar canal
    $stmt_hash->close();

    if ($usuario && password_verify($pass_actual, $usuario['CONTRASENA'])) {
        // La contraseña coincide, guardamos la nueva
        $nueva_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
        
        $stmt_up = $conn->prepare("UPDATE USUARIOS SET CONTRASENA = ? WHERE ID_USUARIO = ?");
        $stmt_up->bind_param("si", $nueva_hash, $id_usuario);
        
        if ($stmt_up->execute()) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error_bd"]);
        }
        $stmt_up->close();
    } else {
        echo json_encode(["status" => "pass_incorrecta"]);
    }
} else {
    echo json_encode(["status" => "campos_vacios"]);
}
$conn->close();