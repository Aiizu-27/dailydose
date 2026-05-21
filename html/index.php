<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAILY DOSE - Tu dosis diaria de café</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <!-- CSS Global -->
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- CSS Componentes -->
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/index.css">
  </head>

  <body>

  <!-- Header / Cabecera de la página -->
  <?php include "includes/header.php"; ?>

  <!-- Contenedor de pantalla inicial -->
  <div class="contenedor-fijo">
    <!-- Video de fondo -->
    <video class="video-fondo" autoplay muted loop>
      <source src="assets/video/VideoInicio.mp4" type="video/mp4">
    </video>

    <!-- Contenido encima del video -->
    <div class="contenido-sobre-video"></div>
  </div>

  <!-- Contenido principal que aparecerá al hacer scroll -->
  <div class="contenido-principal">
    <div class="fila1">
      <div class="div1">
        <h1>Tu dosis diaria de café y productividad</h1>
        <p>Café de especialidad, espacios de trabajo y reservas online en el corazón de Madrid.</p>

        <div class="div1-botones">
          <a href="carta.php" class="btn-principal">Pedir ahora</a>
          <a href="reservas.php" class="btn-secundario">Reservar sala</a>
        </div>
      </div>
      <div class="div2">
        <h2>Descarga la app</h2>
        <p>
        Gestiona tus pedidos, acumula puntos y reserva tu espacio desde cualquier lugar.
        Todo tu Daily Dose en la palma de tu mano.
        </p>
        <div class="imagen-app">
          <img src="assets/img/APP.png">
        </div>
      </div>
    </div>

    <div class="fila2">
      <div class="div3">
        <h2>Tu experiencia en 4 pasos</h2>
        <ul class="pasos">
          <li>Elige tu bebida favorita desde nuestra carta online.</li>
          <li>Haz tu pedido o reserva tu sala en pocos clics.</li>
          <li>Acumula puntos con cada compra.</li>
          <li>Disfruta tu Daily Dose en un entorno pensado para ti.</li>
        </ul>
      </div>
      <div class="div4">
        <h2>Cada café cuenta</h2>
          <p>Con nuestro programa de fidelización, cada compra suma puntos.
          Canjea recompensas, consulta tu historial y disfruta ventajas exclusivas.</p>
          <a href="registro.php" class="btn-principal">Crear cuenta</a>
      </div>
    </div>

    <div class="fila3">
      <div class="div5">
        
      </div>
    </div>
  </div>

<?php include "includes/footer.php"; ?>

<!-- JS -->
<script src="assets/js/script.js"></script>
</body>
</html>