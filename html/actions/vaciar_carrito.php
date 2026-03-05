<?php
session_start();

// Destruimos únicamente la "caja" del carrito, sin cerrar la sesión del usuario
if(isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
}

// Lo devolvemos al carrito (que ahora aparecerá con el mensaje de "Carrito vacío")
header("Location: ../carrito.php");
exit();
?>