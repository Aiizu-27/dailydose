<?php
session_start();
require_once "../includes/config.php";

// ===== SEGURIDAD =====
if (!isset($_SESSION['ROL']) || !in_array($_SESSION['ROL'], ['EMPLEADO', 'ADMIN'])) {
    header("Location: ../index.php");
    exit();
}

// ===== OBTENER EMPLEADOS =====
$stmt = $conn->prepare("CALL sp_obtener_empleados()");
$stmt->execute();
$res_empleados = $stmt->get_result();
$empleados = [];
while ($emp = $res_empleados->fetch_assoc()) {
    $empleados[] = $emp;
}
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

// ===== OBTENER PEDIDOS ACTIVOS =====
$stmt = $conn->prepare("CALL sp_obtener_pedidos_activos()");
$stmt->execute();
$res_pedidos = $stmt->get_result();

$columnas = [
    'PENDIENTE'      => [],
    'EN_PREPARACION' => [],
    'LISTO'          => []
];

$ids_pedidos = [];
$pedidos_map = [];

while ($pedido = $res_pedidos->fetch_assoc()) {
    $columnas[$pedido['ESTADO']][] = $pedido;
    $ids_pedidos[] = $pedido['ID_PEDIDO'];
    $pedidos_map[$pedido['ID_PEDIDO']] = $pedido;
}
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

// ===== OBTENER DETALLE DE CADA PEDIDO =====
$detalles = [];
foreach ($ids_pedidos as $id_pedido) {
    $stmt = $conn->prepare("CALL sp_obtener_detalle_pedido(?)");
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $res = $stmt->get_result();
    $detalles[$id_pedido] = [];
    while ($linea = $res->fetch_assoc()) {
        $detalles[$id_pedido][] = $linea;
    }
    while ($stmt->more_results()) $stmt->next_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Comandas - Daily Dose</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard_trabajador.css">
</head>
<body class="worker-body">

<nav class="worker-nav">
    <div class="nav-left">
        <img src="../assets/img/logo.png" alt="Logo" class="mini-logo">
        <span class="panel-title">DAILY DOSE <small>WORKER</small></span>
    </div>
    <div class="nav-right">
        <span class="worker-info"><?= htmlspecialchars($_SESSION['NOMBRE'] ?? 'Trabajador') ?></span>
        <a href="../actions/auth_logout.php" class="btn-logout-minimal">Salir</a>
    </div>
</nav>

<main class="worker-container">
    <header class="section-header">
        <h1>Tablero de Gestión de Pedidos</h1>
        <div class="status-legend">
            <span class="dot d-pend"></span> Pendiente
            <span class="dot d-prep"></span> Preparando
            <span class="dot d-listo"></span> Listo
        </div>
        <!-- Botón para refrescar sin recargar la página -->
        <button onclick="location.reload()" class="btn-refresh">↻ Actualizar</button>
    </header>

    <div class="kanban-board">
        <?php
        $labels = [
            'PENDIENTE'      => 'Pendiente',
            'EN_PREPARACION' => 'En Preparación',
            'LISTO'          => 'Listo'
        ];
        foreach ($columnas as $fase => $pedidos):
        ?>
        <div class="kanban-columna">
            <div class="col-header">
                <h2><?= $labels[$fase] ?></h2>
                <span class="contador"><?= count($pedidos) ?></span>
            </div>

            <div class="kanban-tickets">
                <?php if (empty($pedidos)): ?>
                    <p class="empty-msg">No hay pedidos</p>
                <?php endif; ?>

                <?php foreach ($pedidos as $pedido): ?>
                <div class="ticket-pedido border-<?= strtolower($fase) ?>" id="ticket-<?= $pedido['ID_PEDIDO'] ?>">

                    <div class="ticket-top">
                        <span class="badge-id">#<?= $pedido['ID_PEDIDO'] ?></span>
                        <span class="badge-mesa">
                            <?= $pedido['NUMERO_MESA'] ? 'Mesa ' . $pedido['NUMERO_MESA'] : 'Para llevar' ?>
                        </span>
                    </div>

                    <div class="ticket-body">
                        <p class="client-name">
                            <strong><?= htmlspecialchars($pedido['CLIENTE_NOMBRE'] ?? 'Anónimo') ?></strong>
                        </p>
                        <?php if ($pedido['BARISTA']): ?>
                            <p class="barista-name">☕ <?= htmlspecialchars($pedido['BARISTA']) ?></p>
                        <?php endif; ?>

                        <!-- Detalle de productos -->
                        <ul class="ticket-productos">
                            <?php foreach ($detalles[$pedido['ID_PEDIDO']] as $linea): ?>
                                <li>
                                    <span class="prod-cantidad"><?= $linea['CANTIDAD'] ?>x</span>
                                    <span class="prod-nombre"><?= htmlspecialchars($linea['PRODUCTO']) ?></span>
                                    <span class="prod-precio"><?= number_format($linea['SUBTOTAL'], 2) ?>€</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="ticket-total">Total: <strong><?= number_format($pedido['TOTAL'], 2) ?>€</strong></p>
                    </div>

                    <div class="ticket-footer">
                        <?php if ($fase === 'PENDIENTE'): ?>
                            <div class="form-asignar">
                                <select id="empleado-<?= $pedido['ID_PEDIDO'] ?>">
                                    <option value="" disabled selected>Asignar barista...</option>
                                    <?php foreach ($empleados as $emp): ?>
                                        <option value="<?= $emp['ID_EMPLEADO'] ?>">
                                            <?= htmlspecialchars($emp['NOMBRE']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button onclick="cambiarEstado(<?= $pedido['ID_PEDIDO'] ?>, 'EN_PREPARACION', 'empleado-<?= $pedido['ID_PEDIDO'] ?>')" 
                                        class="btn-go">▶ Iniciar</button>
                            </div>

                        <?php elseif ($fase === 'EN_PREPARACION'): ?>
                            <button onclick="cambiarEstado(<?= $pedido['ID_PEDIDO'] ?>, 'LISTO')" 
                                    class="btn-action-full btn-listo">✓ Terminado</button>

                        <?php else: ?>
                            <button onclick="cambiarEstado(<?= $pedido['ID_PEDIDO'] ?>, 'ENTREGADO')" 
                                    class="btn-action-full btn-entregar">Entregar al cliente</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
function cambiarEstado(idPedido, nuevoEstado, selectId = null) {
    const formData = new FormData();
    formData.append('id', idPedido);
    formData.append('estado', nuevoEstado);

    // Si hay empleado seleccionado lo añadimos
    if (selectId) {
        const select = document.getElementById(selectId);
        if (!select.value) {
            alert('Selecciona un barista primero');
            return;
        }
        formData.append('id_empleado', select.value);
    }

    fetch('../actions/cambiar_estado.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            // Recargamos solo el tablero sin parpadeo brusco
            location.reload();
        } else {
            alert('Error al cambiar estado: ' + data.status);
        }
    })
    .catch(() => alert('Error de conexión'));
}
</script>

</body>
</html>