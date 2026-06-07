<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (!isset($_SESSION['ROL']) || $_SESSION['ROL'] != 'ADMIN') {
    header("Location: ../index.php");
    exit();
}


$stmt = $conn->prepare("CALL sp_admin_obtener_estadisticas_hoy()");
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

$stmt = $conn->prepare("CALL sp_admin_obtener_usuarios()");
$stmt->execute();
$res_users = $stmt->get_result();
$usuarios = [];
while($row = $res_users->fetch_assoc()) { $usuarios[] = $row; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

$stmt = $conn->prepare("CALL sp_admin_obtener_pedidos_hoy()");
$stmt->execute();
$res_pedidos = $stmt->get_result();
$pedidos_hoy = [];
while($row = $res_pedidos->fetch_assoc()) { $pedidos_hoy[] = $row; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

$stmt = $conn->prepare("CALL sp_admin_obtener_stock_bajo()");
$stmt->execute();
$res_stock = $stmt->get_result();
$stock_bajo = [];
while($row = $res_stock->fetch_assoc()) { $stock_bajo[] = $row; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

$stmt = $conn->prepare("CALL sp_admin_obtener_todos_productos()");
$stmt->execute();
$res_prod = $stmt->get_result();
$productos_carta = [];
while($row = $res_prod->fetch_assoc()) { $productos_carta[] = $row; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();

$stmt = $conn->prepare("CALL sp_obtener_empleados()");
$stmt->execute();
$res_emp = $stmt->get_result();
$empleados = [];
while($row = $res_emp->fetch_assoc()) { $empleados[] = $row; }
while ($stmt->more_results()) $stmt->next_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAILY DOSE - Panel Central</title>
    <link rel="icon" type="assets/image/png" href="../assets/img/APP.png">
    <link rel="stylesheet" href="../assets/css/variables.css?v=2">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/dashboard_admin.css?v=3">
</head>
<body class="admin-body">

<nav class="worker-nav">
    <div class="nav-left">
        <img src="../assets/img/APP.png" alt="Logo Daily Dose" class="mini-logo">
        <span class="panel-title">Daily Dose <small>Panel Administrador</small></span>
    </div>
    <div class="nav-right">
        <span class="Administrador-info">
            <i class="fa-solid fa-user-tie"></i>
            <?= htmlspecialchars($_SESSION['NOMBRE'] ?? 'Trabajador') ?>
        </span>
        <a href="../actions/auth_logout.php" class="btn-logout-minimal">
            <i class="fa-solid fa-right-from-bracket"></i> Salir
        </a>
    </div>
</nav>

<div class="container" style="max-width: 1200px; margin: 110px auto 0;">
    <h2 style="text-align:center;">Centro de Operaciones Globales</h2>
    <p style="text-align:center; margin-bottom:30px;">Admin activo: <strong><?= htmlspecialchars($_SESSION['NOMBRE']) ?></strong></p>

    <details open>
        <summary>Estadísticas del Día</summary>
        <div class="details-content">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                <div style="background: rgba(0,0,0,0.03); padding: 20px; border-radius: 10px; border-left: 4px solid var(--rojo-japones);">
                    <h3 style="font-size: 2.2rem; color: var(--rojo-japones);"><?= number_format($stats['INGRESOS_HOY'] ?? 0, 2) ?> €</h3>
                    <p style="margin:0; opacity: 0.8;">Ingresos de Hoy</p>
                </div>
                <div style="background: rgba(0,0,0,0.03); padding: 20px; border-radius: 10px; border-left: 4px solid var(--verde-pastel-oscuro);">
                    <h3 style="font-size: 2.2rem; color: var(--verde-pastel-oscuro);"><?= $stats['PEDIDOS_HOY'] ?? 0 ?></h3>
                    <p style="margin:0; opacity: 0.8;">Pedidos Procesados</p>
                </div>
                <div style="background: rgba(0,0,0,0.03); padding: 20px; border-radius: 10px; border-left: 4px solid #ffc107;">
                    <h3 style="font-size: 2.2rem; color: #ffc107;"><?= $stats['TICKET_MEDIO'] ?? 0 ?> €</h3>
                    <p style="margin:0; opacity: 0.8;">Ticket Medio</p>
                </div>
            </div>
        </div>
    </details>

    <details>
        <summary>Gestión de Usuarios y Personal</summary>
        <div class="details-content">
            <h4 style="color:var(--rojo-japones); margin-bottom: 10px;">Contratación de Personal</h4>
            <form id="form-add-trabajador" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:15px; margin-bottom:30px;">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellidos" placeholder="Apellidos" required>
                <input type="email" name="email" placeholder="usuario@dailydose.es" pattern="[a-z0-9._%+-]+@dailydose\.es$" title="Debe usar correo @dailydose.es" required>
                <input type="text" name="puesto" placeholder="Puesto (ej: BARISTA, ENCARGADO)" required>
                <input type="number" step="0.01" name="salario" placeholder="Salario Mensual" required>
                <input type="password" name="contrasena" placeholder="Clave temporal" required>
                <button type="submit">Dar de Alta</button>
            </form>

            <h4 style="color:var(--rojo-japones); margin-bottom: 10px;">Control de Roles de la Plataforma</h4>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Nombre completo</th><th>Email</th><th>Rol del Sistema</th><th>Acción Crítica</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $user): ?>
                        <tr>
                            <td><?= $user['ID_USUARIO'] ?></td>
                            <td><?= htmlspecialchars($user['NOMBRE'] . ' ' . $user['APELLIDOS']) ?></td>
                            <td><?= htmlspecialchars($user['EMAIL']) ?></td>
                            <td>
                                <form class="form-cambiar-rol" style="display:inline-flex; gap:5px;">
                                    <input type="hidden" name="id_usuario" value="<?= $user['ID_USUARIO'] ?>">
                                    <select name="nuevo_rol" style="padding: 5px;">
                                        <option value="CLIENTE" <?= $user['ROL']=='CLIENTE'?'selected':'' ?>>CLIENTE</option>
                                        <option value="EMPLEADO" <?= $user['ROL']=='EMPLEADO'?'selected':'' ?>>EMPLEADO</option>
                                        <option value="ADMIN" <?= $user['ROL']=='ADMIN'?'selected':'' ?>>ADMIN</option>
                                    </select>
                                    <button type="submit" style="padding:5px 10px;">✓</button>
                                </form>
                            </td>
                            <td>
                                <button class="btn-baja-directa button btn-danger" data-id="<?= $user['ID_USUARIO'] ?>" style="padding: 5px 12px; font-size:0.85rem;">Revocar Acceso</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </details>

    <details>
        <summary>Pedidos del Día</summary>
        <div class="details-content" style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Cód. Pedido</th><th>Hora</th><th>Ubicación</th><th>Cliente</th><th>Total</th><th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pedidos_hoy)): ?><tr><td colspan="6">No hay comandas registradas hoy.</td></tr><?php endif; ?>
                    <?php foreach($pedidos_hoy as $p): ?>
                    <tr>
                        <td><strong>#<?= $p['ID_PEDIDO'] ?></strong></td>
                        <td><?= date("H:i", strtotime($p['FECHA'])) ?></td>
                        <td><?= $p['NUMERO_MESA'] ? "Mesa ".$p['NUMERO_MESA'] : "Para llevar" ?></td>
                        <td><?= htmlspecialchars($p['CLIENTE_NOMBRE'] ?? 'Consumidor Local') ?></td>
                        <td style="color:var(--verde-pastel-oscuro); font-weight:bold;"><?= number_format($p['TOTAL'], 2) ?> €</td>
                        <td><span class="badge" style="background: rgba(0,0,0,0.05); padding:4px 10px; border-radius:20px; font-size:0.8rem;"><?= $p['ESTADO'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>

    <details>
        <summary>Stock e Inventario Crítico</summary>
        <div class="details-content">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Insumo / Producto</th><th>Categoría</th><th>Existencias</th><th>Estado Almacén</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($stock_bajo)): ?><tr><td colspan="5" style="color:var(--verde-pastel-oscuro); font-weight:bold;">✓ Todo el inventario está por encima del nivel de seguridad.</td></tr><?php endif; ?>
                    <?php foreach($stock_bajo as $s): ?>
                    <tr style="background: rgba(244, 43, 29, 0.02);">
                        <td><?= $s['ID_PRODUCTO'] ?></td>
                        <td><strong><?= htmlspecialchars($s['NOMBRE']) ?></strong></td>
                        <td><?= $s['CATEGORIA'] ?></td>
                        <td style="color:var(--rojo-japones); font-weight:bold;"><?= $s['STOCK'] ?> uds.</td>
                        <td><span style="color:#ffc107; font-weight:bold;">REABASTECER</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>

    <details>
        <summary>Gestión de la Carta y Productos</summary>
        <div class="details-content">
            <h4 style="color:var(--rojo-japones); margin-bottom: 15px;">Añadir Nuevo Producto al Menú</h4>
            <form id="form-add-producto" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:15px; margin-bottom:30px;">
                <input type="text" name="nombre" placeholder="Nombre (ej: Flat White)" required>
                <input type="text" name="categoria" placeholder="Categoría (ej: CAFES, DULCES)" required>
                <input type="number" step="0.01" name="precio" placeholder="Precio de Venta" required>
                <input type="number" name="stock" placeholder="Stock Inicial" required>
                <button type="submit">Agregar a la Carta</button>
            </form>

            <h4 style="color:var(--rojo-japones); margin-bottom: 10px;">Catálogo de Productos Activos</h4>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Ítem</th><th>Categoría</th><th>Precio</th><th>Stock Disponible</th><th>Ajuste Rápido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos_carta as $prod): ?>
                        <tr>
                            <td><?= $prod['ID_PRODUCTO'] ?></td>
                            <td><?= htmlspecialchars($prod['NOMBRE']) ?></td>
                            <td><?= $prod['CATEGORIA'] ?></td>
                            <td><strong><?= number_format($prod['PRECIO'], 2) ?> €</strong></td>
                            <td><?= $prod['STOCK'] ?> uds.</td>
                            <td>
                                <button class="btn-toggle-producto" data-id="<?= $prod['ID_PRODUCTO'] ?>" style="background:var(--verde-pastel-oscuro); padding: 5px 10px; font-size:0.8rem;">Modificar Stock</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </details>

    <details>
        <summary>Gestión de Turnos y Planificación</summary>
        <div class="details-content">
            <h4 style="color:var(--rojo-japones); margin-bottom: 15px;">Fijar Cuadrante Semanal</h4>
            <form id="form-add-turno" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
                <select name="id_empleado" required>
                    <option value="" disabled selected>Seleccionar Barista...</option>
                    <?php foreach($empleados as $emp): ?>
                        <option value="<?= $emp['ID_EMPLEADO'] ?>"><?= htmlspecialchars($emp['NOMBRE'] . ' ' . $emp['APELLIDOS'] . ' - ' . $emp['PUESTO']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label style="display:flex; flex-direction:column; gap:4px; font-size:0.8rem; opacity:0.8;">
                    Semana a partir del (lunes)
                    <input type="date" name="fecha_inicio" value="<?= date('Y-m-d', strtotime('monday this week')) ?>" required>
                </label>
                <select name="bloque_turno" required>
                    <option value="MAÑANA">Mañana</option>
                    <option value="TARDE">Tarde</option>
                </select>

                <fieldset style="grid-column: 1 / -1; display:flex; flex-wrap:wrap; gap:12px; border:1px solid var(--cristal-borde); border-radius:8px; padding:10px 14px;">
                    <legend style="font-size:0.8rem; opacity:0.8; padding:0 6px;">Días que trabaja (deja 2 sin marcar = días libres)</legend>
                    <?php
                    $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                    foreach ($dias_semana as $offset => $nombre_dia):
                        $marcado = $offset < 5; // por defecto Lun-Vie trabajan, Sáb y Dom libres
                    ?>
                        <label style="display:flex; align-items:center; gap:5px; font-size:0.85rem;">
                            <input type="checkbox" name="dias[]" value="<?= $offset ?>" <?= $marcado ? 'checked' : '' ?>>
                            <?= $nombre_dia ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <button type="submit">Asignar Cuadrante</button>
            </form>
        </div>
    </details>

    <div class="logout-container" style="margin-top: 35px; margin-bottom: 3rem;">
        <a href="../actions/auth_logout.php" class="btn-logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Cerrar Sesión
        </a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('form-add-trabajador').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('../actions/admin_add_trabajador.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if(data.status === 'ok') { alert('¡Contratación formalizada con éxito!'); location.reload(); }
            else { alert('Error: ' + data.status); }
        });
    });

    document.querySelectorAll('.form-cambiar-rol').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('../actions/admin_cambiar_rol.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json()).then(data => {
                if(data.status === 'ok') { alert('Permisos de cuenta actualizados.'); }
                else { alert('Error operativo al cambiar rol.'); }
            });
        });
    });

    document.querySelectorAll('.btn-baja-directa').forEach(btn => {
        btn.addEventListener('click', function() {
            if(!confirm('¿Seguro que deseas eliminar esta cuenta? Esta acción es irreversible.')) return;
            const f = new FormData(); f.append('id_baja', this.getAttribute('data-id'));
            fetch('../actions/admin_baja_usuario.php', { method: 'POST', body: f })
            .then(res => res.json()).then(data => {
                if(data.status === 'ok') { alert('Cuenta eliminada de la base de datos.'); location.reload(); }
                else { alert('Error al dar de baja.'); }
            });
        });
    });

    document.getElementById('form-add-producto').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('../actions/admin_add_producto.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if(data.status === 'ok') { alert('¡Producto insertado en la carta digital!'); location.reload(); }
            else { alert('Error al añadir producto.'); }
        });
    });

    document.querySelectorAll('.btn-toggle-producto').forEach(btn => {
        btn.addEventListener('click', function() {
            const nuevoStock = prompt("Introduce las unidades reales en almacén:");
            if (nuevoStock === null || nuevoStock.trim() === "" || isNaN(nuevoStock)) return;
            const f = new FormData();
            f.append('id_producto', this.getAttribute('data-id'));
            f.append('nuevo_stock', nuevoStock);
            fetch('../actions/admin_update_stock.php', { method: 'POST', body: f })
            .then(res => res.json()).then(data => {
                if(data.status === 'ok') { alert('Inventario sincronizado.'); location.reload(); }
                else { alert('Error al ajustar existencias.'); }
            });
        });
    });

    document.getElementById('form-add-turno').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('../actions/admin_add_turno.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if(data.status === 'ok') { alert('Cuadrante semanal guardado y publicado (Lunes a Domingo).'); }
            else { alert('Error al registrar el cuadrante semanal.'); }
        });
    });
});
</script>
</body>
</html>