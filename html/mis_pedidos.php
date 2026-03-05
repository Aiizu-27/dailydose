<?php
session_start();
require_once "includes/config.php";

// 1. SEGURIDAD: Solo usuarios logueados pueden ver esta página
if (!isset($_SESSION['ID_USUARIO'])) {
    header("Location: registro.php");
    exit();
}

$id_usuario = $_SESSION['ID_USUARIO'];

// 2. CONSULTA SQL: Obtenemos los pedidos del cliente logueado
// Relacionamos PEDIDOS con CLIENTES para filtrar por el ID_USUARIO de la sesión
$sql = "SELECT p.* FROM PEDIDOS p 
        JOIN CLIENTES c ON p.ID_CLIENTE = c.ID_CLIENTE 
        WHERE c.ID_USUARIO = ? 
        ORDER BY p.FECHA DESC, p.ID_PEDIDO DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - DAILY DOSE</title>

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
    <link rel="stylesheet" href="assets/css/mis_pedidos.css?v=<?php echo time(); ?>">
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="pedidos-container">
    
    <div class="cabecera-carrito">
        <h1>Mis Pedidos</h1>
        <p>Consulta el historial y el estado de tus consumiciones.</p>
    </div>

    <div class="lista-pedidos">
        <?php if ($resultado->num_rows > 0): ?>
            
            <?php while ($pedido = $resultado->fetch_assoc()): ?>
                <div class="tarjeta-cristal pedido-card">
                    
                    <div class="info-pedido">
                        <h3>Pedido #<?php echo $pedido['ID_PEDIDO']; ?></h3>
                        <p>Fecha: <strong><?php echo date("d/m/Y", strtotime($pedido['FECHA'])); ?></strong></p>
                        <p>Mesa: <strong><?php echo $pedido['NUMERO_MESA'] ? $pedido['NUMERO_MESA'] : 'Para llevar'; ?></strong></p>
                        <p>Total: <span class="precio-total"><?php echo number_format($pedido['TOTAL'], 2); ?> €</span></p>
                    </div>

                    <div class="estado-contenedor">
                        <span class="estado-badge <?php echo $pedido['ESTADO']; ?>">
                            <?php echo str_replace('_', ' ', $pedido['ESTADO']); ?>
                        </span>
                    </div>

                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="tarjeta-cristal" style="text-align: center; padding: 50px;">
                <p>Todavía no has realizado ningún pedido.</p>
                <br>
                <a href="carta.php" class="btn-principal">Ver la Carta</a>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include "includes/footer.php"; ?>

<script src="assets/js/script.js"></script>
</body>
</html>