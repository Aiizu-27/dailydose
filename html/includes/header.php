<?php
// 1. Iniciamos sesión solo si no estaba ya iniciada en el archivo principal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. RED DE SEGURIDAD: Definimos la ruta base automáticamente si no existe.
if (!defined('BASE_URL')) {
    define('BASE_URL', '/'); 
}
?>

<header>
  <div class="logo">DAILY DOSE</div>

  <button id="menuBtn" aria-label="Abrir menú">☰</button>
  <button id="menuCerrar" aria-label="Cerrar menú">&times;</button>

  <div class="menu-container" id="menu">
    <nav class="menu-header">
      <ul>
        <li><a href="<?php echo BASE_URL; ?>index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'activo' : ''; ?>">Inicio</a></li>
        <li><a href="<?php echo BASE_URL; ?>carta.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'carta.php' ? 'activo' : ''; ?>">Carta</a></li>
        <li><a href="<?php echo BASE_URL; ?>promociones.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'promociones.php' ? 'activo' : ''; ?>">Promociones</a></li>
        
        <?php if (isset($_SESSION['ID_USUARIO'])): ?>
            <li><a href="<?php echo BASE_URL; ?>mis_pedidos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'mis_pedidos.php' ? 'activo' : ''; ?>">Mis Pedidos</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <div class="header-right">
    
    <a href="<?php echo BASE_URL; ?>carrito.php" class="iconoCarrito" title="Ver mi carrito">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      
      <?php 
        $cantidad_total = 0;
        if(isset($_SESSION['carrito'])) {
            // Sumamos la cantidad de cada producto (no solo los productos distintos)
            foreach($_SESSION['carrito'] as $item) {
                $cantidad_total += $item['cantidad'];
            }
        }
      ?>
      <?php if($cantidad_total > 0): ?>
          <span class="badge-carrito"><?= $cantidad_total ?></span>
      <?php endif; ?>
    </a>
    <label class="switch">
      <input type="checkbox" id="toggleTema">
      <span class="slider">
        <span class="circle">
          <svg class="icono sol" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="5" fill="var(--rojo-japones)" />
            <g stroke="var(--rojo-japones)" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="4"/>
              <line x1="12" y1="20" x2="12" y2="23"/>
              <line x1="1" y1="12" x2="4" y2="12"/>
              <line x1="20" y1="12" x2="23" y2="12"/>
              <line x1="4.2" y1="4.2" x2="6" y2="6"/>
              <line x1="18" y1="18" x2="19.8" y2="19.8"/>
              <line x1="4.2" y1="19.8" x2="6" y2="18"/>
              <line x1="18" y1="6" x2="19.8" y2="4.2"/>
            </g>
          </svg>
          <svg class="icono luna" viewBox="0 0 24 24">
            <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" fill="var(--rojo-japones)"/>
          </svg>
        </span>
      </span>
    </label>

    <?php if(isset($_SESSION['ROL'])): ?>
      
      <?php 
        // Lógica para enviar al usuario a su panel correspondiente usando BASE_URL
        $rol_actual = strtolower($_SESSION['ROL']);
        $link_panel = BASE_URL . 'panel/dashboard_cliente.php'; 
        
        if($rol_actual == 'admin') {
            $link_panel = BASE_URL . 'panel/dashboard_admin.php';
        } elseif($rol_actual == 'empleado' || $rol_actual == 'trabajador') {
            $link_panel = BASE_URL . 'panel/dashboard_trabajador.php';
        }
      ?>
      <a href="<?php echo $link_panel; ?>" class="iconoUsuario" title="Ir a Mi Panel">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="green" width="28" height="28">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M12 12a5 5 0 100-10 5 5 0 000 10z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
        </svg>
      </a>

    <?php else: ?>
      
      <a href="<?php echo BASE_URL; ?>registro.php" class="iconoUsuario" title="Iniciar sesión / Registrarse">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="28" height="28">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M12 12a5 5 0 100-10 5 5 0 000 10z"/>
        </svg>
      </a>

    <?php endif; ?>

  </div>
</header>