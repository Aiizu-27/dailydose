<?php
session_start();
require_once "includes/config.php";

// 1. Comprobamos si hay sesión, pero NO expulsamos al usuario
$esta_logueado = isset($_SESSION['ROL']);
$puntos_actuales = 0;
$result_promos = null;

if ($esta_logueado) {
    $id_usuario = $_SESSION['ID_USUARIO'];

    // 2. OBTENER LOS PUNTOS ACTUALES DEL CLIENTE
    $stmt = $conn->prepare("SELECT PUNTOS FROM CLIENTES WHERE ID_USUARIO = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        $puntos_actuales = $row['PUNTOS'] ?? 0;
    }
    $stmt->close();

    // 3. OBTENER TODAS LAS PROMOCIONES
    $sql_promos = "SELECT * FROM PROMOCIONES ORDER BY PUNTOS_REQUERIDOS ASC";
    $result_promos = $conn->query($sql_promos);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAILY DOSE - Tu dosis diaria de café</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/promociones.css?v=1">
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="promo-container">

    <section class="header-puntos">
        <h1>Centro de Recompensas</h1>
        <p>Canjea tus Daily Points por productos gratis y descuentos exclusivos.</p>
        
        <?php if ($esta_logueado): ?>
            <div class="marcador-puntos">
                <?= htmlspecialchars($puntos_actuales) ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($esta_logueado): ?>

        <section class="grid-promos">
            <?php if ($result_promos && $result_promos->num_rows > 0): ?>
                
                <?php while ($promo = $result_promos->fetch_assoc()): ?>
                    <div class="promo-card">
                        <div>
                            <h3><?= htmlspecialchars($promo['NOMBRE']) ?></h3>
                            <p><?= htmlspecialchars($promo['DESCRIPCION']) ?></p>
                            <div class="coste-puntos">
                                Valor: <?= $promo['PUNTOS_REQUERIDOS'] ?> puntos
                            </div>
                        </div>

                        <?php if ($puntos_actuales >= $promo['PUNTOS_REQUERIDOS']): ?>
                            <form action="actions/canjear_promo.php" method="POST">
                                <input type="hidden" name="id_promocion" value="<?= $promo['ID_PROMOCION'] ?>">
                                <button type="submit" class="btn-canjear btn-activo">¡Canjear Ahora!</button>
                            </form>
                        <?php else: ?>
                            <?php $puntos_faltantes = $promo['PUNTOS_REQUERIDOS'] - $puntos_actuales; ?>
                            <div class="btn-canjear btn-inactivo">
                                Te faltan <?= $puntos_faltantes ?> puntos
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <p>Actualmente no hay promociones disponibles. ¡Vuelve pronto!</p>
            <?php endif; ?>
        </section>

    <?php else: ?>

        <section>
            <div class="promo-card">
                <h3>¡Únete al Club!</h3>
                <p>
                    Para poder ver nuestras promociones exclusivas, acumular puntos con tus pedidos y conseguir productos gratis, necesitas tener una cuenta.
                </p>
                <a href="registro.php" class="btn-canjear btn-activo">
                    Iniciar sesión / Registrarse
                </a>
            </div>
        </section>

    <?php endif; ?>

</main>

<?php include "includes/footer.php"; ?>

<script src="assets/js/script.js"></script>

</body>
</html>