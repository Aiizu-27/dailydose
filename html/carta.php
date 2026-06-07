<?php
session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/../secure_config/config.php';

if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$stmt = $conn->prepare("CALL sp_carta_obtener_categories()");
$stmt->execute();
$res_cat = $stmt->get_result();
$categorias = [];
while ($cat = $res_cat->fetch_assoc()) {
    $categorias[] = $cat;
}
while ($conn->more_results()) $conn->next_result();
$stmt->close();

$stmt = $conn->prepare("CALL sp_carta_obtener_productos()");
$stmt->execute();
$res_prod = $stmt->get_result();
$productos = [];
while ($prod = $res_prod->fetch_assoc()) {
    $productos[] = $prod;
}
while ($conn->more_results()) $conn->next_result();
$stmt->close();

$fecha_hoy = date('Y-m-d');
$stmt = $conn->prepare("CALL sp_carta_obtener_especialidad(?)");
$stmt->bind_param("s", $fecha_hoy);
$stmt->execute();
$especial = $stmt->get_result()->fetch_assoc();
while ($conn->more_results()) $conn->next_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta - DAILY DOSE</title>
    <link rel="icon" type="image/png" href="assets/img/APP.png">

    <link rel="stylesheet" href="assets/css/variables.css?v=2">
    <link rel="stylesheet" href="assets/css/style.css?v=2">

    <link rel="stylesheet" href="assets/css/header.css?v=2">
    <link rel="stylesheet" href="assets/css/footer.css?v=3">
    <link rel="stylesheet" href="assets/css/carta.css?v=8">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="carta-layout">

    <nav class="menu-categorias">
        <h3>Categorías</h3>
        <ul>
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $cat): 
                    $idCat = strtolower(str_replace(' ', '-', $cat['CATEGORIA'])); 
                ?>
                    <li><a href="#<?= $idCat ?>"><?= htmlspecialchars($cat['CATEGORIA']) ?></a></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No hay categorías disponibles</li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="columna-productos">
    <?php
    $categoriaActual = "";

    if (!empty($productos)) {
        foreach ($productos as $row) {

            if ($categoriaActual != $row['CATEGORIA']) {
                if ($categoriaActual != "") echo "</ul>";
                $categoriaActual = $row['CATEGORIA'];
                $idCategoria = strtolower(str_replace(' ', '-', $categoriaActual));
                
                echo "<div class='cat-heading'>";
                echo "<h2 id='$idCategoria'>" . htmlspecialchars($categoriaActual) . "</h2>";
                echo "<div class='cat-linea'></div>";
                echo "</div>";
                
                echo "<ul class='productos'>";
            }
            ?>
            <li>
                <!-- La imagen o placeholder son hijos directos del li (Columna 1 del Grid) -->
                <?php if (!empty($row['RUTA_IMAGEN'])): ?>
                    <img src="<?= htmlspecialchars($row['RUTA_IMAGEN']) ?>"
                         alt="<?= htmlspecialchars($row['NOMBRE']) ?>" 
                         class="img-producto">
                <?php else: ?>
                    <div class="img-placeholder">☕</div>
                <?php endif; ?>

                <!-- Información del producto (Columna 2, Fila 1) -->
                <div class="info-prod">
                    <span class="grid-nombre nombre"><?= htmlspecialchars($row['NOMBRE']) ?></span>
                </div>

                <!-- Meta y acciones (Columna 2, Fila 2) -->
                <div class="meta-prod">
                    <span class="precio"><?= number_format($row['PRECIO'], 2) ?>€</span>

                    <?php if (isset($_SESSION['ROL']) && strtolower($_SESSION['ROL']) === 'cliente'): ?>
                        <form class="form-carrito" method="POST" action="actions/add_carrito.php">
                            <input type="hidden" name="id_producto" value="<?= $row['ID_PRODUCTO'] ?>">
                            <input type="hidden" name="producto" value="<?= htmlspecialchars($row['NOMBRE']) ?>">
                            <input type="hidden" name="precio" value="<?= $row['PRECIO'] ?>">
                            <input type="hidden" name="stock" value="<?= $row['STOCK'] ?>">
                            <button type="submit" class="btn-carrito">+</button>
                        </form>
                    <?php else: ?>
                        <a href="registro.php" class="btn-login-pedido">Inicia sesión</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php
        }
        echo "</ul>";
    } else {
        echo "<p class='mensaje-vacio'>No hay productos disponibles en el menú actual.</p>";
    }
    ?>
    </section>

    <aside class="columna-lateral">
    <?php if ($especial): ?>
        <div class="tarjeta-especialidad">
            <div class="header-especial">
                <h3>DAILY SPECIAL</h3>
                <p class="fechas">Edición Limitada</p>
            </div>

            <div class="seccion-grano">
                <h4>Origen del Grano</h4>
                <p class="origen"><?= htmlspecialchars($especial['ORIGEN_GRANO']); ?></p>
                <p class="notas"><i>"<?= htmlspecialchars($especial['NOTAS_CATA']); ?>"</i></p>
                <div class="etiqueta"><?= htmlspecialchars($especial['TUESTE']); ?></div>
            </div>

            <hr>

            <div class="seccion-metodo">
                <h4>Recomendación de Barista</h4>
                <p class="metodo"><?= htmlspecialchars($especial['METODO_FILTRO']); ?></p>
                <p class="desc-filtro"><?= htmlspecialchars($especial['DESCRIPCION_FILTRO']); ?></p>
            </div>

            <div class="destacado-seasonal">
                <h4>Bebida de Temporada</h4>
                <p class="seasonal-nombre"><?= htmlspecialchars($especial['SEASONAL_NOMBRE']); ?></p>
                <p class="seasonal-desc"><?= htmlspecialchars($especial['SEASONAL_DESCRIPCION']); ?></p>
            </div>
        </div>
    <?php else: ?>
        <p class="mensaje-vacio">No hay daily special disponible hoy.</p>
    <?php endif; ?>
    </aside>

</main>

<?php include "includes/footer.php"; ?>

<script src="assets/js/script.js"></script>
</body>
</html>