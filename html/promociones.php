<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

$esta_logueado = isset($_SESSION['ROL']);
$puntos_actuales = 0;
$recompensas = [];

if ($esta_logueado) {
    $id_usuario = $_SESSION['ID_USUARIO'];

    $stmt = $conn->prepare("CALL sp_promos_obtener_puntos_cliente(?)");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res_puntos = $stmt->get_result();
    if ($row = $res_puntos->fetch_assoc()) {
        $puntos_actuales = $row['PUNTOS'] ?? 0;
    }
    while ($conn->more_results()) $conn->next_result();
    $stmt->close();

    $stmt = $conn->prepare("CALL sp_promos_obtener_recompensas()");
    $stmt->execute();
    $res_recompensas = $stmt->get_result();
    while ($row = $res_recompensas->fetch_assoc()) {
        $recompensas[] = $row;
    }
    while ($conn->more_results()) $conn->next_result();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAILY DOSE - Tu dosis diaria de café</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <link rel="stylesheet" href="assets/css/variables.css?v=2">
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/header.css?v=3">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/promociones.css?v=1">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="promo-container">

    <section class="header-puntos">
        <h1>Centro de Recompensas</h1>
        <p>Canjea tus Daily Points por productos gratis y descuentos exclusivos.</p>

        <?php if (isset($_GET['canje'])): ?>
            <?php if ($_GET['canje'] === 'ok'): ?>
                <div class="aviso-canje aviso-ok">¡Canje realizado con éxito! Tus puntos han sido actualizados.</div>
            <?php else: ?>
                <?php
                    $motivos = [
                        'puntos_insuficientes' => 'No tienes suficientes puntos para esta recompensa.',
                        'sin_stock' => 'Esta recompensa no tiene stock disponible.',
                        'recompensa_no_encontrada' => 'La recompensa seleccionada no existe.',
                        'cliente_no_encontrado' => 'No se pudo identificar tu cuenta de cliente.',
                        'datos_invalidos' => 'No se han recibido los datos correctos para el canje.',
                    ];
                    $motivo = $_GET['motivo'] ?? '';
                ?>
                <div class="aviso-canje aviso-error"><?= htmlspecialchars($motivos[$motivo] ?? 'No se ha podido realizar el canje.') ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($esta_logueado): ?>
            <div class="marcador-puntos">
                <?= htmlspecialchars($puntos_actuales) ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($esta_logueado): ?>

        <section class="grid-promos">
            <?php if (!empty($recompensas)): ?>
                
                <?php foreach ($recompensas as $promo): ?>
                    <div class="promo-card">
                        <div>
                            <h3><?= htmlspecialchars($promo['NOMBRE']) ?></h3>
                            <p><?= htmlspecialchars($promo['DESCRIPCION']) ?></p>
                            <div class="coste-puntos">
                                Valor: <?= $promo['COSTE_PUNTOS'] ?> puntos
                            </div>
                        </div>

                        <?php if ($puntos_actuales >= $promo['COSTE_PUNTOS']): ?>
                            <form action="actions/canjear_promo.php" method="POST">
                                <input type="hidden" name="id_promocion" value="<?= $promo['ID_RECOMPENSA'] ?>">
                                <button type="submit" class="btn-canjear btn-activo">¡Canjear Ahora!</button>
                            </form>
                        <?php else: ?>
                            <?php $puntos_faltantes = $promo['COSTE_PUNTOS'] - $puntos_actuales; ?>
                            <div class="btn-canjear btn-inactivo">
                                Te faltan <?= $puntos_faltantes ?> puntos
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

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