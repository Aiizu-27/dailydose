<?php
session_start();
require_once "includes/config.php";

// Si por algún motivo de seguridad extra quieres asegurarte de que nadie sin sesión entre aquí copiando la URL:
if (!isset($_SESSION['ROL'])) {
    header("Location: registro.php");
    exit();
}

// 1. Obtenemos el carrito de la sesión (si no existe, lo creamos como un array vacío)
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pedido = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Pedido - DAILY DOSE</title>

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/carrito.css">
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="carrito-container">
    
    <div class="cabecera-carrito">
        <h1>Tu Bandeja</h1>
        <p>Revisa tu dosis diaria antes de confirmar el pedido.</p>
    </div>

    <?php if(empty($carrito)): ?>
        <div class="tarjeta-cristal carrito-vacio">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2>Tu carrito está vacío</h2>
            <p>Aún no has añadido ningún producto. ¡Echa un vistazo a nuestra carta!</p>
            <a href="carta.php" class="btn-principal">Ir a la Carta</a>
        </div>

    <?php else: ?>
        <div class="carrito-layout">
            
            <div class="tarjeta-cristal lista-productos">
                <h3>Productos</h3>
                <ul>
                    <?php foreach($carrito as $id_producto => $item): ?>
                        <?php 
                            // Calculamos el subtotal de este producto (Precio x Cantidad)
                            $subtotal = $item['precio'] * $item['cantidad'];
                            // Lo sumamos al Total Global
                            $total_pedido += $subtotal;
                        ?>
                        <li class="item-carrito">
                            <div class="item-info">
                                <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                                <span class="item-precio"><?= number_format($item['precio'], 2) ?> € x <?= $item['cantidad'] ?></span>
                            </div>
                            <div class="item-subtotal">
                                <?= number_format($subtotal, 2) ?> €
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tarjeta-cristal resumen-pedido">
                <h3>Resumen del Pedido</h3>
                
                <div class="fila-total">
                    <span>TOTAL a pagar:</span>
                    <span class="precio-total"><?= number_format($total_pedido, 2) ?> €</span>
                </div>

                <form action="actions/procesar_carrito.php" method="POST" class="form-checkout">
                    <div class="cajas">
                        <input type="number" name="numero_mesa" id="numero_mesa" placeholder=" " min="1" max="50">
                        <label for="numero_mesa">Nº de Mesa (Solo local)</label>
                    </div>

                    <button type="submit" class="btn-principal w-100">Confirmar Pedido</button>
                </form>

                <form action="actions/vaciar_carrito.php" method="POST" style="margin-top: 15px;">
                    <button type="submit" class="btn-peligro w-100">Vaciar Carrito</button>
                </form>
            </div>

        </div>
    <?php endif; ?>

</main>

<?php include "includes/footer.php"; ?>
<script src="assets/js/script.js"></script>
</body>
</html>