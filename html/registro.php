<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - DAILY DOSE</title>
<link rel="icon" type="image/png" href="assets/img/APP.png">
<link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- CSS Componentes -->
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/registro.css">
</head>
<body>

<!-- Header / Cabecera de la página -->
<?php include "includes/header.php"; ?>

<!-- FORMULARIO -->
<div class="registroContenido">
    <div class="formulario">
        <div class="form-tabs">
            <button id="tabLogin" class="active">Iniciar sesión</button>
            <button id="tabRegistro">Registrarse</button>
        </div>

        <!-- FORMULARIO LOGIN -->
        <form id="formLogin">
            <div class="cajas">
                <input type="email" name="correo" placeholder=" " required>
                <label>Correo electrónico</label>
            </div>
            <div class="cajas password-box">
                <input type="password" name="contrasena" id="claveLogin" placeholder=" " required>
                <label>Contraseña</label>
                <span class="icono-ojo" id="iconoOjoLogin">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <!-- Ojo abierto -->
                        <path class="ojo-abierto" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle class="ojo-abierto" cx="12" cy="12" r="3"/>
                        <!-- Ojo cerrado -->
                        <path class="ojo-cerrado" stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                        <path class="ojo-cerrado" stroke-linecap="round" stroke-linejoin="round" d="M10.477 10.477a3 3 0 104.243 4.243"/>
                        <path class="ojo-cerrado" stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322C3.423 7.51 7.36 4.5 12 4.5s8.573 3.007 9.963 7.178c.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5S3.423 16.49 2.036 12.322z"/>
                    </svg>
                </span>
            </div>
            <button type="submit">Iniciar sesión</button>
        </form>

        <!-- FORMULARIO REGISTRO -->
        <form id="formRegistro" style="display:none;">
            <div class="cajas">
                <input type="text" name="nombre" placeholder=" " required>
                <label>Nombre</label>
            </div>
            <div class="cajas">
                <input type="text" name="apellidos" placeholder=" " required>
                <label>Apellidos</label>
            </div>
            <div class="cajas">
                <input type="email" name="correo" placeholder=" " required>
                <label>Correo electrónico</label>
            </div>
            <div class="cajas password-box">
                <input type="password" name="contrasena" id="claveRegistro" placeholder=" " required>
                <label>Contraseña</label>
                <span class="icono-ojo" id="iconoOjoRegistro">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <!-- Ojo abierto -->
                        <path class="ojo-abierto" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle class="ojo-abierto" cx="12" cy="12" r="3"/>
                        <!-- Ojo cerrado -->
                        <path class="ojo-cerrado" stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                        <path class="ojo-cerrado" stroke-linecap="round" stroke-linejoin="round" d="M10.477 10.477a3 3 0 104.243 4.243"/>
                        <path class="ojo-cerrado" stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322C3.423 7.51 7.36 4.5 12 4.5s8.573 3.007 9.963 7.178c.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5S3.423 16.49 2.036 12.322z"/>
                    </svg>
                </span>
            </div>
            <div class="cajas">
                <input type="text" name="telefono" placeholder=" " required>
                <label>Teléfono</label>
            </div>
            <button type="submit">Registrarse</button>
        </form>


<!-- Enlace al JS externo -->
<script src="assets/js/script.js"></script>

</body>
</html>