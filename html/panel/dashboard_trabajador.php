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
 
$ids_pedidos  = [];
$mesas_ocupadas = []; // id_mesa => pedido
 
while ($pedido = $res_pedidos->fetch_assoc()) {
    $columnas[$pedido['ESTADO']][] = $pedido;
    $ids_pedidos[] = $pedido['ID_PEDIDO'];
    if ($pedido['ID_MESA']) {
        $mesas_ocupadas[$pedido['ID_MESA']] = $pedido;
    }
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
 
// ===== OBTENER MESAS =====
$stmt = $conn->prepare("CALL sp_obtener_mesas()");
$stmt->execute();
$res_mesas = $stmt->get_result();
$mesas = [];
while ($mesa = $res_mesas->fetch_assoc()) {
    $mesas[] = $mesa;
}
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
 
// ===== CONTADOR PEDIDOS DEL DÍA =====
$stmt = $conn->prepare("CALL sp_contar_pedidos_hoy()");
$stmt->execute();
$res_contador = $stmt->get_result();
$contador_hoy = $res_contador->fetch_assoc()['TOTAL'] ?? 0;
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
 
// ===== TURNOS SEMANA ACTUAL =====
$lunes = date('Y-m-d', strtotime('monday this week'));
$stmt = $conn->prepare("CALL sp_obtener_turnos_semanas(?)");
$stmt->bind_param("s", $lunes);
$stmt->execute();
$res_turnos = $stmt->get_result();
$turnos_raw = [];
while ($t = $res_turnos->fetch_assoc()) {
    $turnos_raw[$t['DIA']][$t['ID_EMPLEADO']] = $t['TURNO'];
}
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
 
// Estadísticas rápidas
$mesas_libres   = count(array_filter($mesas, fn($m) => $m['ESTADO'] === 'LIBRE'));
$mesas_ocupadas_count = count(array_filter($mesas, fn($m) => $m['ESTADO'] === 'OCUPADA'));
 
// Días de la semana actual
$dias_semana = [];
for ($i = 0; $i < 7; $i++) {
    $dias_semana[] = date('Y-m-d', strtotime($lunes . " +$i days"));
}
 
$nombres_dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$hoy = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Comandas — Daily Dose</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard_trabajador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="worker-body">
 
<!-- ── NAV ──────────────────────────────── -->
<nav class="worker-nav">
    <div class="nav-left">
        <img src="../assets/img/logo.png" alt="Logo Daily Dose" class="mini-logo">
        <span class="panel-title">Daily Dose <small>Worker Panel</small></span>
    </div>
    <div class="nav-right">
        <span class="worker-info">
            <i class="fa-solid fa-user-tie"></i>
            <?= htmlspecialchars($_SESSION['NOMBRE'] ?? 'Trabajador') ?>
        </span>
        <a href="../actions/auth_logout.php" class="btn-logout-minimal">
            <i class="fa-solid fa-right-from-bracket"></i> Salir
        </a>
    </div>
</nav>
 
<main class="worker-container">
 
    <!-- ── HEADER ────────────────────────── -->
    <header class="section-header">
        <h1>Tablero de Gestión</h1>
        <div class="status-legend">
            <span class="dot d-pend"></span> Pendiente
            <span class="dot d-prep"></span> Preparando
            <span class="dot d-listo"></span> Listo
        </div>
        <div class="refresh-bar" style="margin:0;">
            <span class="refresh-info" id="refresh-countdown">
                <i class="fa-solid fa-rotate"></i> Actualizando en <span id="cuenta">30</span>s
            </span>
            <button onclick="location.reload()" class="btn-refresh">↻ Actualizar</button>
        </div>
    </header>
 
    <!-- ── STATS ─────────────────────────── -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num" style="color: var(--verde-pastel-oscuro);">
                <?= $mesas_libres ?>
            </div>
            <div class="stat-label">Mesas libres</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color: var(--rojo-japones);">
                <?= $mesas_ocupadas_count ?>
            </div>
            <div class="stat-label">Mesas ocupadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color: var(--texto-principal);">
                <?= count($columnas['PENDIENTE']) ?>
            </div>
            <div class="stat-label">Pendientes ahora</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color: var(--texto-principal);">
                <?= $contador_hoy ?>
            </div>
            <div class="stat-label">Pedidos hoy</div>
        </div>
    </div>
 
    <!-- ── MAPA DE MESAS ──────────────────── -->
    <section class="mapa-section">
        <div class="zona-label">
            <i class="fa-solid fa-map-location-dot"></i> Sala — zona clientes
        </div>
        <div class="mesas-grid">
            <?php foreach ($mesas as $mesa):
                $id_mesa   = $mesa['ID_MESA'];
                $num_mesa  = $mesa['NUMERO_MESA'];
                $estado    = strtolower($mesa['ESTADO']);
                $inactiva  = ($mesa['ESTADO'] === 'MANTENIMIENTO' || $mesa['ESTADO'] === 'RESERVADA');
                $clase     = $inactiva ? 'inactiva' : $estado;
 
                // Buscar pedido activo de esta mesa
                $pedido_mesa = null;
                foreach ($columnas as $col) {
                    foreach ($col as $p) {
                        if ($p['ID_MESA'] == $id_mesa) {
                            $pedido_mesa = $p;
                            break 2;
                        }
                    }
                }
            ?>
            <div class="mesa <?= $clase ?>">
                <div>
                    <div class="mesa-num">Mesa <?= $num_mesa ?></div>
                    <div class="mesa-estado">
                        <?php if ($inactiva): ?>
                            <?= ucfirst(strtolower($mesa['ESTADO'])) ?>
                        <?php elseif ($estado === 'libre'): ?>
                            Libre
                        <?php elseif ($estado === 'ocupada'): ?>
                            Ocupada
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($pedido_mesa): ?>
                <div class="mesa-pedido">
                    #<?= $pedido_mesa['ID_PEDIDO'] ?> · <?= htmlspecialchars($pedido_mesa['CLIENTE_NOMBRE'] ?? 'Anónimo') ?><br>
                    <?= ucfirst(strtolower(str_replace('_', ' ', $pedido_mesa['ESTADO']))) ?>
                    · <?= number_format($pedido_mesa['TOTAL'], 2) ?>€
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="leyenda">
            <div class="leyenda-item"><span class="dot d-listo" style="background:var(--verde-pastel-oscuro)"></span> Libre</div>
            <div class="leyenda-item"><span class="dot" style="background:var(--rojo-japones)"></span> Ocupada</div>
            <div class="leyenda-item"><span class="dot" style="background:#E9A83A"></span> Listo para entregar</div>
            <div class="leyenda-item"><span class="dot" style="background:#aaa"></span> No operativa</div>
        </div>
    </section>
 
    <!-- ── PARA LLEVAR ────────────────────── -->
    <?php
    $pedidos_llevar = [];
    foreach ($columnas as $col) {
        foreach ($col as $p) {
            if (!$p['ID_MESA']) $pedidos_llevar[] = $p;
        }
    }
    ?>
    <?php if (!empty($pedidos_llevar)): ?>
    <section class="llevar-section">
        <div class="zona-label">
            <i class="fa-solid fa-bag-shopping"></i> Para llevar
        </div>
        <div class="llevar-list">
            <?php foreach ($pedidos_llevar as $p): ?>
            <div class="llevar-item">
                <span>Pedido #<?= $p['ID_PEDIDO'] ?> · <?= htmlspecialchars($p['CLIENTE_NOMBRE'] ?? 'Anónimo') ?></span>
                <?php
                $badge_class = match($p['ESTADO']) {
                    'PENDIENTE'      => 'badge-pend',
                    'EN_PREPARACION' => 'badge-prep',
                    'LISTO'          => 'badge-listo',
                    default          => ''
                };
                $badge_texto = match($p['ESTADO']) {
                    'PENDIENTE'      => 'Pendiente',
                    'EN_PREPARACION' => 'En preparación',
                    'LISTO'          => 'Listo',
                    default          => $p['ESTADO']
                };
                ?>
                <span class="badge <?= $badge_class ?>"><?= $badge_texto ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
 
    <!-- ── KANBAN ─────────────────────────── -->
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
                    <p class="empty-msg">Sin pedidos</p>
                <?php endif; ?>
 
                <?php foreach ($pedidos as $pedido): ?>
                <div class="ticket-pedido border-<?= strtolower($fase) ?>" id="ticket-<?= $pedido['ID_PEDIDO'] ?>">
 
                    <div class="ticket-top">
                        <span class="badge-id">#<?= $pedido['ID_PEDIDO'] ?></span>
                        <span class="badge-mesa">
                            <?= $pedido['NUMERO_MESA'] ? 'Mesa ' . $pedido['NUMERO_MESA'] : 'Llevar' ?>
                        </span>
                    </div>
 
                    <div class="ticket-body">
                        <p class="client-name">
                            <?= htmlspecialchars($pedido['CLIENTE_NOMBRE'] ?? 'Anónimo') ?>
                        </p>
                        <?php if ($pedido['BARISTA']): ?>
                        <p class="barista-name">
                            <i class="fa-solid fa-mug-hot"></i> <?= htmlspecialchars($pedido['BARISTA']) ?>
                        </p>
                        <?php endif; ?>
 
                        <ul class="ticket-productos">
                            <?php foreach ($detalles[$pedido['ID_PEDIDO']] as $linea): ?>
                            <li>
                                <span class="prod-cantidad"><?= $linea['CANTIDAD'] ?>x</span>
                                <span class="prod-nombre"><?= htmlspecialchars($linea['PRODUCTO']) ?></span>
                                <span class="prod-precio"><?= number_format($linea['SUBTOTAL'], 2) ?>€</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="ticket-total">
                            Total: <strong><?= number_format($pedido['TOTAL'], 2) ?>€</strong>
                        </p>
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
                            <button onclick="cambiarEstado(<?= $pedido['ID_PEDIDO'] ?>, 'EN_PREPARACION', 'empleado-<?= $pedido['ID_PEDIDO'] ?>')" class="btn-go">
                                ▶
                            </button>
                        </div>
 
                        <?php elseif ($fase === 'EN_PREPARACION'): ?>
                        <button onclick="cambiarEstado(<?= $pedido['ID_PEDIDO'] ?>, 'LISTO')" class="btn-action-full btn-listo">
                            ✓ Terminado
                        </button>
 
                        <?php else: ?>
                        <button onclick="cambiarEstado(<?= $pedido['ID_PEDIDO'] ?>, 'ENTREGADO')" class="btn-action-full btn-entregar">
                            Entregar al cliente
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
 
    <!-- ── CALENDARIO DE TURNOS ───────────── -->
    <section class="turnos-section" style="margin-top: 1.5rem;">
        <div class="zona-label">
            <i class="fa-solid fa-calendar-days"></i> Turnos del equipo
        </div>
 
        <div class="semana-tabs">
            <?php for ($s = 0; $s < 4; $s++):
                $inicio_tab = date('d/m', strtotime($lunes . " +$s weeks"));
                $fin_tab    = date('d/m', strtotime($lunes . " +$s weeks +6 days"));
            ?>
            <button class="semana-tab <?= $s === 0 ? 'active' : '' ?>"
                    onclick="mostrarSemana(<?= $s ?>, this)">
                <?= $s === 0 ? 'Esta semana' : "Sem. $inicio_tab – $fin_tab" ?>
            </button>
            <?php endfor; ?>
        </div>
 
        <?php for ($s = 0; $s < 4; $s++):
            $dias_bloque = [];
            for ($d = 0; $d < 7; $d++) {
                $dias_bloque[] = date('Y-m-d', strtotime($lunes . " +$s weeks +$d days"));
            }
        ?>
        <div class="semana-bloque" id="semana-<?= $s ?>" style="<?= $s > 0 ? 'display:none;' : '' ?> overflow-x: auto;">
            <table class="tabla-turnos">
                <thead>
                    <tr>
                        <th style="text-align:left;">Empleado</th>
                        <?php foreach ($dias_bloque as $i => $dia): ?>
                        <th class="<?= $dia === $hoy ? 'hoy' : '' ?>">
                            <?= $nombres_dias[$i] ?><br>
                            <span style="font-size:0.65rem;"><?= date('d/m', strtotime($dia)) ?></span>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empleados as $emp): ?>
                    <tr>
                        <td class="nombre-emp"><?= htmlspecialchars($emp['NOMBRE']) ?></td>
                        <?php foreach ($dias_bloque as $dia): ?>
                        <td>
                            <?php $turno = $turnos_raw[$dia][$emp['ID_EMPLEADO']] ?? null; ?>
                            <?php if ($turno === 'MAÑANA'): ?>
                                <span class="turno-pill pill-m">Mañana</span>
                            <?php elseif ($turno === 'TARDE'): ?>
                                <span class="turno-pill pill-t">Tarde</span>
                            <?php else: ?>
                                <span class="turno-pill pill-libre">Libre</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endfor; ?>
 
        <div class="leyenda" style="margin-top: 0.75rem;">
            <div class="leyenda-item"><span class="turno-pill pill-m">Mañana</span></div>
            <div class="leyenda-item"><span class="turno-pill pill-t">Tarde</span></div>
            <div class="leyenda-item"><span class="turno-pill pill-libre">Libre</span></div>
        </div>
    </section>
 
</main>
 
<script>
// ── AUTO REFRESCO ────────────────────────────
let segundos = 30;
const cuentaEl = document.getElementById('cuenta');
 
setInterval(() => {
    segundos--;
    if (cuentaEl) cuentaEl.textContent = segundos;
    if (segundos <= 0) location.reload();
}, 1000);
 
// ── CAMBIAR ESTADO ───────────────────────────
function cambiarEstado(idPedido, nuevoEstado, selectId = null) {
    const formData = new FormData();
    formData.append('id', idPedido);
    formData.append('estado', nuevoEstado);
 
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
            location.reload();
        } else {
            alert('Error al cambiar estado: ' + data.status);
        }
    })
    .catch(() => alert('Error de conexión'));
}
 
// ── TABS SEMANAS ────────────────────────────
function mostrarSemana(index, btn) {
    document.querySelectorAll('.semana-bloque').forEach((el, i) => {
        el.style.display = i === index ? 'block' : 'none';
    });
    document.querySelectorAll('.semana-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
}
</script>
 
</body>
</html>