<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (!isset($_SESSION['ROL'])) {
    header("Location: registro.php");
    exit();
}

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pedido = 0;

$stmt = $conn->prepare("CALL sp_obtener_mesas()");
$stmt->execute();
$res_mesas = $stmt->get_result();
$mesas = [];
while ($mesa = $res_mesas->fetch_assoc()) { $mesas[] = $mesa; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Pedido - DAILY DOSE</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <link rel="stylesheet" href="assets/css/variables.css?v=2">
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/carrito.css?v=3">
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="carrito-container">
    
    <div class="cabecera-carrito">
        <h1>Tu Bandeja</h1>
        <p>Revisa tu dosis diaria antes de confirmar el pedido.</p>
    </div>

    <!-- 🟢 CORREGIDO: Eliminados los estilos inline opacos; ahora usa tu clase .error-mesa con efecto cristal -->
    <?php if (isset($_GET['error']) && $_GET['error'] == 'mesa_no_valida'): ?>
        <div class="error-mesa">
            El número de mesa introducido no existe en el local. Por favor, compruébalo.
        </div>
    <?php endif; ?>

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
                            $subtotal = $item['precio'] * $item['cantidad'];
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
                        <select name="numero_mesa" id="numero_mesa">
                            <option value="">Para llevar / Sin mesa</option>
                            <?php foreach ($mesas as $m):
                                $libre = ($m['ESTADO'] === 'LIBRE');
                            ?>
                                <option value="<?= $m['NUMERO_MESA'] ?>" <?= $libre ? '' : 'disabled' ?>>
                                    Mesa <?= $m['NUMERO_MESA'] ?><?= $libre ? '' : ' (ocupada)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="numero_mesa">Nº de Mesa (Solo local)</label>
                    </div>

                    <button type="submit" class="btn-principal w-100">Confirmar Pedido</button>
                </form>

                <!-- 🟢 CORREGIDO: Removido el style="margin-top: 15px" duplicado; el espaciado lo gobierna el CSS -->
                <form action="actions/vaciar_carrito.php" method="POST">
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