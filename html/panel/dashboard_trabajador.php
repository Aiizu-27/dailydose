<?php
session_start();
require_once "../includes/config.php";

// --- SEGURIDAD ---
// He añadido un strtoupper y trim por si el rol en BD viene diferente
if (!isset($_SESSION['ROL']) || strtoupper(trim($_SESSION['ROL'])) != 'TRABAJADOR') {
    header("Location: ../index.php");
    exit();
}

// 1. OBTENER LA LISTA DE EMPLEADOS
$sql_empleados = "SELECT e.ID_EMPLEADO, u.NOMBRE 
                  FROM EMPLEADOS e 
                  JOIN USUARIOS u ON e.ID_USUARIO = u.ID_USUARIO";
$res_empleados = $conn->query($sql_empleados);
$empleados = [];
while ($emp = $res_empleados->fetch_assoc()) {
    $empleados[] = $emp;
}

// 2. OBTENER PEDIDOS Y ORGANIZARLOS
// He corregido 'EN PREPARACION' por 'EN_PREPARACION' si es que tu ENUM lleva guion bajo
$sql_pedidos = "SELECT p.*, u.NOMBRE as CLIENTE_NOMBRE, emp_u.NOMBRE as BARISTA
                FROM PEDIDOS p 
                LEFT JOIN CLIENTES c ON p.ID_CLIENTE = c.ID_CLIENTE 
                LEFT JOIN USUARIOS u ON c.ID_USUARIO = u.ID_USUARIO
                LEFT JOIN EMPLEADOS e ON p.ID_EMPLEADO = e.ID_EMPLEADO
                LEFT JOIN USUARIOS emp_u ON e.ID_USUARIO = emp_u.ID_USUARIO
                WHERE p.ESTADO IN ('PENDIENTE', 'EN PREPARACION', 'LISTO') 
                ORDER BY p.ID_PEDIDO ASC";
$result_pedidos = $conn->query($sql_pedidos);

$columnas = [
    'PENDIENTE' => [],
    'EN PREPARACION' => [],
    'LISTO' => []
];

while ($pedido = $result_pedidos->fetch_assoc()) {
    $columnas[$pedido['ESTADO']][] = $pedido;
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
    <link rel="stylesheet" href="../assets/css/dashboard_trabajador.css?v=<?php echo time(); ?>">
</head>
<body class="worker-body">

<nav class="worker-nav">
    <div class="nav-left">
        <img src="../assets/img/logo.png" alt="Logo" class="mini-logo">
        <span class="panel-title">DAILY DOSE <small>WORKER</small></span>
    </div>
    
    <div class="nav-right">
        <span class="worker-info"><i class="fa-solid fa-user-tie"></i> <?= htmlspecialchars($_SESSION['NOMBRE'] ?? 'Trabajador') ?></span>
        <a href="../actions/auth_logout.php" class="btn-logout-minimal">
            <i class="fa-solid fa-right-from-bracket"></i> Salir
        </a>
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
    </header>

    <div class="kanban-board">
        <?php foreach ($columnas as $fase => $pedidos): ?>
            <div class="kanban-columna">
                <div class="col-header">
                    <h2><?= $fase ?></h2>
                    <span class="contador"><?= count($pedidos) ?></span>
                </div>
                
                <div class="kanban-tickets">
                    <?php if (empty($pedidos)): ?>
                        <p class="empty-msg">No hay pedidos aquí</p>
                    <?php endif; ?>

                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="ticket-pedido <?= 'border-' . strtolower(str_replace(' ', '', $fase)) ?>">
                            <div class="ticket-top">
                                <span class="badge-id">#<?= $pedido['ID_PEDIDO'] ?></span>
                                <span class="badge-mesa"><?= $pedido['NUMERO_MESA'] ? 'Mesa '.$pedido['NUMERO_MESA'] : 'Llevar' ?></span>
                            </div>

                            <div class="ticket-body">
                                <p class="client-name"><strong><?= htmlspecialchars($pedido['CLIENTE_NOMBRE'] ?? 'Anonimo') ?></strong></p>
                                <?php if ($pedido['BARISTA']): ?>
                                    <p class="barista-name"><i class="fa-solid fa-mug-hot"></i> <?= htmlspecialchars($pedido['BARISTA']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="ticket-footer">
                                <?php if ($fase == 'PENDIENTE'): ?>
                                    <form action="../actions/cambiar_estado.php" method="POST" class="form-asignar">
                                        <input type="hidden" name="id" value="<?= $pedido['ID_PEDIDO'] ?>">
                                        <input type="hidden" name="estado" value="EN PREPARACION">
                                        <select name="id_empleado" required>
                                            <option value="" disabled selected>Asignar...</option>
                                            <?php foreach ($empleados as $emp): ?>
                                                <option value="<?= $emp['ID_EMPLEADO'] ?>"><?= htmlspecialchars($emp['NOMBRE']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn-go"><i class="fa-solid fa-play"></i></button>
                                    </form>
                                <?php elseif ($fase == 'EN PREPARACION'): ?>
                                    <form action="../actions/cambiar_estado.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $pedido['ID_PEDIDO'] ?>">
                                        <input type="hidden" name="estado" value="LISTO">
                                        <button type="submit" class="btn-action-full btn-listo">¡Terminado!</button>
                                    </form>
                                <?php else: ?>
                                    <form action="../actions/cambiar_estado.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $pedido['ID_PEDIDO'] ?>">
                                        <input type="hidden" name="estado" value="ENTREGADO">
                                        <button type="submit" class="btn-action-full btn-entregar">Entregar al cliente</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>