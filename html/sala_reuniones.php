<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de Reuniones - DAILY DOSE</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
    <link rel="stylesheet" href="assets/css/sala_reuniones.css">
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="sala-container">
    
    <div class="cabecera-sala">
        <div class="icon-central"></div>
        <h1>Espacio Co-Working & Reuniones</h1>
        <span class="badge-proximamente">Módulo en Desarrollo</span>
    </div>

    <div class="sala-layout">
        
        <div class="tarjeta-cristal sala-descripcion">
            <p>
                Estamos ultimando los detalles de nuestro espacio exclusivo más tecnológico. Muy pronto podrás reservar la <strong>Sala de Reuniones</strong> directamente desde nuestra aplicación para tus eventos privados, juntas de trabajo o sesiones de co-working mientras profesas tu devoción por nuestro mejor café de especialidad.
            </p>
        </div>

        <div class="tarjeta-cristal sala-equipamiento">
            <h3>Equipamiento de la Sala (Mesa 7)</h3>
            <ul class="features-list">
                <li><strong>Capacidad máxima:</strong> Mesa presidencial para un aforo de hasta 8 personas.</li>
                <li><strong>Sistemas Audiovisuales:</strong> Pantalla Smart TV de 55" con cámara integrada dedicada a videoconferencias ultra-HD.</li>
                <li><strong>Automatización:</strong> Panel exterior de Reserva Digital sincronizado para control de acceso y horarios en tiempo real.</li>
                <li><strong>Conectividad:</strong> Puntos de carga inductiva dedicados y segmentación de red Wi-Fi 6 de alta velocidad.</li>
            </ul>
        </div>

        <div class="sala-acciones">
            <p class="aviso-disculpas">
                Disculpa las molestias. Estamos trabajando en el despliegue del módulo de gobernanza de reservas digitales.
            </p>
            <a href="carta.php" class="btn-principal">
                Volver a la Carta
            </a>
        </div>
        
    </div>

</main>

<?php include "includes/footer.php"; ?>

<script src="assets/js/script.js"></script>
</body>
</html>