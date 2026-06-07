<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

$mas_vendidos = [];
$stmt = $conn->prepare("CALL sp_obtener_productos_mas_vendidos(?)");
$limite = 8;
$stmt->bind_param("i", $limite);
$stmt->execute();
$res_vendidos = $stmt->get_result();
while ($row = $res_vendidos->fetch_assoc()) { $mas_vendidos[] = $row; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAILY DOSE - Tu dosis diaria de café</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <!-- CSS Global -->
    <link rel="stylesheet" href="assets/css/variables.css?v=2">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- CSS Componentes -->
    <link rel="stylesheet" href="assets/css/header.css?v=3">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/index.css?v=5">
  </head>

  <body>

  <?php include "includes/header.php"; ?>

  <div class="contenedor-fijo">
    <!-- Video de fondo -->
    <video class="video-fondo" autoplay muted loop>
      <source src="assets/video/VideoInicio.mp4" type="video/mp4">
    </video>

    <div class="contenido-sobre-video"></div>
  </div>

  <div class="contenido-principal">
    <div class="fila1">
      <div class="div1">
        <h1>Tu dosis diaria de café y productividad</h1>
        <p>Café de especialidad, espacios de trabajo y reservas online en el corazón de Madrid.</p>

        <div class="div1-botones">
          <a href="carta.php" class="btn-principal">Pedir ahora</a>
          <a href="sala_reuniones.php" class="btn-secundario">Reservar sala</a>
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
        <h2>Lo más pedido</h2>
        <p>Los favoritos de nuestra comunidad, elegidos por ti.</p>

        <?php if (!empty($mas_vendidos)): ?>
        <div class="carrusel-vendidos">
          <button class="carrusel-flecha carrusel-prev" aria-label="Anterior">&#10094;</button>

          <div class="carrusel-pista">
            <?php foreach ($mas_vendidos as $prod): ?>
              <a href="carta.php" class="carrusel-tarjeta">
                <?php if (!empty($prod['RUTA_IMAGEN'])): ?>
                  <img src="<?= htmlspecialchars($prod['RUTA_IMAGEN']) ?>" alt="<?= htmlspecialchars($prod['NOMBRE']) ?>">
                <?php else: ?>
                  <div class="carrusel-placeholder">☕</div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($prod['NOMBRE']) ?></h3>
              </a>
            <?php endforeach; ?>
          </div>

          <button class="carrusel-flecha carrusel-next" aria-label="Siguiente">&#10095;</button>
        </div>
        <?php else: ?>
          <p>Aún no hay datos de ventas suficientes para mostrar lo más pedido.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

<?php include "includes/footer.php"; ?>

<!-- JS -->
<script src="assets/js/script.js"></script>
</body>
</html>