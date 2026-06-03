<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (!isset($_SESSION['ID_USUARIO'])) {
    header("Location: registro.php");
    exit();
}

$id_usuario = $_SESSION['ID_USUARIO'];
$pedidos = [];

$stmt = $conn->prepare("CALL sp_obtener_pedidos_cliente(?)");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

while ($row = $resultado->fetch_assoc()) {
    $pedidos[] = $row;
}

while ($conn->more_results()) $conn->next_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - DAILY DOSE</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/mis_pedidos.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="pedidos-container" style="max-width: 900px; margin: 120px auto 50px auto; padding: 0 20px;">
    
    <div class="pedidos-header" style="text-align: center; margin-bottom: 40px;">
        <h2 style="font-family: 'CooperBold', sans-serif; color: var(--rojo-japones); font-size: 2.2rem;">Tu Historial de Pedidos</h2>
        <p style="opacity: 0.7;">Consulta el estado de tus comandas actuales y revive tus mejores dosis de café.</p>
    </div>

    <section class="lista-pedidos">
        <?php if (!empty($pedidos)): ?>
            
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido-card" style="background: var(--cristal-fondo); border: 1px solid var(--cristal-borde); border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px var(--cristal-sombra); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    
                    <div class="pedido-info">
                        <h3 style="font-family: 'CooperBold', sans-serif; margin: 0 0 5px 0; font-size: 1.2rem;">
                            Pedido #<?= $pedido['ID_PEDIDO'] ?>
                        </h3>
                        <p style="margin: 0; font-size: 0.9rem; opacity: 0.6;">
                            Fecha: <?= date("d/m/Y - H:i", strtotime($pedido['FECHA'])) ?>
                        </p>
                        <p style="margin: 5px 0 0 0; font-size: 0.85rem; font-weight: bold; color: var(--rojo-japones);">
                            <?= $pedido['NUMERO_MESA'] ? "Consumido en Mesa " . $pedido['NUMERO_MESA'] : "Para llevar / Recoger" ?>
                        </p>
                    </div>

                    <div class="pedido-meta" style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span class="pedido-total" style="font-size: 1.4rem; font-family: 'CooperBold', sans-serif; color: var(--verde-pastel-oscuro);">
                            <?= number_format($pedido['TOTAL'], 2) ?> €
                        </span>
                        
                        <span class="badge-estado" style="background: rgba(0,0,0,0.04); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?= htmlspecialchars($pedido['ESTADO']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="pedido-card" style="background: var(--cristal-fondo); text-align: center; padding: 40px; border-radius: 16px; border: 1px solid var(--cristal-borde);">
                <p style="opacity: 0.7; font-size: 1.1rem; margin-bottom: 20px;">Aún no has realizado ningún pedido en DailyDose.</p>
                <a href="carta.php" class="btn" style="display: inline-block; background: var(--rojo-japones); color: white; padding: 10px 20px; border-radius: 8px; font-family: 'CooperBold', sans-serif;">
                    Ver nuestra Carta
                </a>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php include "includes/footer.php"; ?>

<script src="assets/js/script.js"></script>
</body>
</html>