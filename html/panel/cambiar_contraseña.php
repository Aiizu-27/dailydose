<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - DAILY DOSE</title>
<link rel="icon" type="../assets/image/png" href="assets/img/APP.png">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/header.css">
<link rel="stylesheet" href="assets/css/footer.css">
</head>
<body>

<!-- Header / Cabecera de la página -->
<?php include "includes/header.php"; ?>

<?php
session_start();
if (!isset($_SESSION['ID_USUARIO']) || !$_SESSION['CAMBIAR_PASSWORD']) {
    // No tiene que cambiar la contraseña o no está logueado
    header("Location: ../index.php");
    exit;
}
?>

<!-- CAMBIO DE CONTRASEÑA -->
<div class="registroContenido">
    <div class="formulario">
            <h2>Cambiar Contraseña</h2>
        <!-- FORMULARIO CAMBIO DE CONTRASEÑA -->
        <form action="../actions/auth_cambiar_pass.php" method="POST">
            <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
            <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña" required>
            <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['ID_USUARIO']; ?>">
            <button type="submit">Actualizar contraseña</button>
        </form>
        

<!-- Enlace al JS externo -->
<script src="assets/js/script.js"></script>

</body>
</html>