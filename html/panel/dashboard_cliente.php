<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (!isset($_SESSION['ROL']) || strtolower($_SESSION['ROL']) !== 'cliente') {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['ID_USUARIO'];

$stmt = $conn->prepare("CALL sp_cliente_obtener_perfil(?)");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
while ($conn->more_results()) $conn->next_result();
$stmt->close();

$id_cliente_actual = $cliente['ID_CLIENTE'] ?? 0;

$ultimos_pedidos = [];
$favoritos = [];

if ($id_cliente_actual > 0) {
    $stmt_pedidos = $conn->prepare("CALL sp_cliente_obtener_ultimos_pedidos(?)");
    $stmt_pedidos->bind_param("i", $id_cliente_actual);
    $stmt_pedidos->execute();
    $res_pedidos = $stmt_pedidos->get_result();
    while ($ped = $res_pedidos->fetch_assoc()) {
        $ultimos_pedidos[] = $ped;
    }
    while ($conn->more_results()) $conn->next_result();
    $stmt_pedidos->close();

    $stmt_favs = $conn->prepare("CALL sp_cliente_obtener_favoritos(?)");
    $stmt_favs->bind_param("i", $id_cliente_actual);
    $stmt_favs->execute();
    $res_favs = $stmt_favs->get_result();
    while ($fav = $res_favs->fetch_assoc()) {
        $favoritos[] = $fav;
    }
    while ($conn->more_results()) $conn->next_result();
    $stmt_favs->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - DailyDose</title>
    <link rel="icon" type="assets/image/png" href="../assets/img/APP.png">

    <link rel="stylesheet" href="../assets/css/variables.css?v=2">
    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/dashboard_cliente.css?v=2">
</head>
<body>

<?php include "../includes/header.php"; ?>

<main class="dashboard-container">
    
    <div class="welcome-header">
        <h2>¡Hola, <?= htmlspecialchars($cliente['NOMBRE'] ?? 'Cliente') ?>!</h2>
        <p>Bienvenido a tu panel de DailyDose. Aquí tienes el control de tu cuenta.</p>
    </div>

    <div class="perfil-seccion">
        <div class="datos-personales">
            <h3>Mis Datos Personal</h3>
            <p><strong>Nombre:</strong> <?= htmlspecialchars(($cliente['NOMBRE'] ?? '') . ' ' . ($cliente['APELLIDOS'] ?? '')) ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($cliente['EMAIL'] ?? '') ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['TELEFONO'] ?? 'No especificado') ?></p>
        </div>

        <div class="puntos-card">
            <p class="titulo-puntos">Daily Points</p>
            <div class="amount-puntos cantidad-puntos">
                <?= htmlspecialchars($cliente['PUNTOS'] ?? '0') ?>
            </div>
            <p class="desc-puntos">¡Canjéalos por recompensas!</p>
            <a href="../promociones.php" class="btn" style="margin-top:10px; display:inline-block; text-align:center;">Ver Premios</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        
        <div class="caja-panel">
            <h3>Últimos Pedidos</h3>
            <?php if (!empty($ultimos_pedidos)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_pedidos as $pedido): ?>
                        <tr>
                            <td><strong>#<?= $pedido['ID_PEDIDO'] ?></strong></td>
                            <td><?= date("d/m/Y", strtotime($pedido['FECHA'])) ?></td>
                            <td style="color:var(--verde-pastel-oscuro); font-weight:bold;"><?= number_format($pedido['TOTAL'], 2) ?>€</td>
                            <td><span style="font-size:0.85rem; opacity:0.8; font-weight:bold;"><?= $pedido['ESTADO'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="opacity:0.7; margin-top:15px;">Aún no has realizado ningún pedido. ¡Estrenemos tu cuenta!</p>
                <a href="../carta.php" class="btn">Explorar la Carta</a>
            <?php endif; ?>
        </div>

        <div class="caja-panel">
            <h3>Tus Favoritos</h3>
            <?php if (!empty($favoritos)): ?>
                <p style="opacity:0.7; margin-bottom:15px;">Tus 3 productos más consumidos en el local:</p>
                <ul style="list-style:none; padding:0; margin:0;">
                    <?php foreach ($favoritos as $fav): ?>
                    <li style="padding: 12px 0; border-bottom: 1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between;">
                        <span>☕ <strong><?= htmlspecialchars($fav['NOMBRE']) ?></strong></span>
                        <span style="opacity:0.6; font-size:0.9rem;"><?= $fav['VECES_PEDIDO'] ?> unidades pedidas</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="opacity:0.7; margin-top:15px;">Pide tus cafés favoritos para empezar a ver tus estadísticas personales.</p>
            <?php endif; ?>
        </div>

    </div>

    <div class="logout-container" style="margin-top: 35px;">
        <a href="../actions/auth_logout.php" class="btn-logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Cerrar Sesión
        </a>
    </div>

</main>

<?php include "../includes/footer.php"; ?>

<script src="../assets/js/script.js"></script>
</body>
</html>