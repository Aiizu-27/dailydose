<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta - DAILY DOSE</title>

    <!-- CSS Global -->
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- CSS Componentes -->
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/carta.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="carta-layout">

<?php
include "includes/config.php";

// COMPROBAR CONEXIÓN
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

/* =========================
   MENÚ DE CATEGORÍAS
========================= */
$sqlCategorias = "SELECT DISTINCT CATEGORIA FROM PRODUCTOS ORDER BY CATEGORIA";
$resultCategorias = $conn->query($sqlCategorias);
?>

<nav class="menu-categorias">
    <h3>Categorías</h3>
    <ul>
        <?php
        if ($resultCategorias && $resultCategorias->num_rows > 0) {
            while ($cat = $resultCategorias->fetch_assoc()) {
                $idCat = strtolower(str_replace(' ', '-', $cat['CATEGORIA']));
                echo "<li><a href='#$idCat'>" . htmlspecialchars($cat['CATEGORIA']) . "</a></li>";
            }
        } else {
            echo "<li>No hay categorías disponibles</li>";
        }
        ?>
    </ul>
</nav>

<?php
/* =========================
   PRODUCTOS
========================= */
$sqlProductos = "SELECT ID_PRODUCTO, NOMBRE, CATEGORIA, PRECIO, STOCK, RUTA_IMAGEN 
                 FROM PRODUCTOS 
                 ORDER BY CATEGORIA, NOMBRE";
$resultProductos = $conn->query($sqlProductos);

/* =========================
   ESPECIALIDAD DEL DÍA
========================= */
$fechaComparar = date('Y-m-d');

$sqlEspecialidad = "SELECT * FROM ESPECIALIDAD_ACTUAL
                    WHERE ? BETWEEN FECHA_INICIO AND FECHA_FIN
                    LIMIT 1";

$stmt = $conn->prepare($sqlEspecialidad);
$stmt->bind_param("s", $fechaComparar);
$stmt->execute();
$resultEspecialidad = $stmt->get_result();

// Fallback si no hay especialidad actual
if ($resultEspecialidad->num_rows == 0) {
    $sqlEspecialidad = "SELECT * FROM ESPECIALIDAD_ACTUAL LIMIT 1";
    $resultEspecialidad = $conn->query($sqlEspecialidad);
}

$especial = $resultEspecialidad->fetch_assoc();
?>

<!--COLUMNA PRODUCTOS-->
<section class="columna-productos">
<?php
$categoriaActual = "";

if ($resultProductos && $resultProductos->num_rows > 0) {
    while ($row = $resultProductos->fetch_assoc()) {

        // CAMBIO DE CATEGORÍA
        if ($categoriaActual != $row['CATEGORIA']) {
            if ($categoriaActual != "") echo "</ul>";
            $categoriaActual = $row['CATEGORIA'];
            $idCategoria = strtolower(str_replace(' ', '-', $categoriaActual));
            echo "<h2 id='$idCategoria'>" . htmlspecialchars($categoriaActual) . "</h2>";
            echo "<ul class='productos'>";
        }

        echo "<li>";
        echo "<div class='info-prod'>";

        if (!empty($row['RUTA_IMAGEN'])) {
            echo "<img src='assets/img/productos/" . htmlspecialchars($row['RUTA_IMAGEN']) . "' 
                alt='" . htmlspecialchars($row['NOMBRE']) . "' 
                class='img-producto'>";
        }

        echo "<span class='nombre'>" . htmlspecialchars($row['NOMBRE']) . "</span>";
        echo "</div>";

        // PRECIO + LÓGICA DE BOTÓN AÑADIR AL CARRITO
        echo "<div class='meta-prod'>";
        echo "<span class='precio'>" . number_format($row['PRECIO'], 2) . "€</span>";

        // MÁGIA AQUÍ: Comprobamos si la sesión es de un cliente
        if (isset($_SESSION['ROL']) && strtolower($_SESSION['ROL']) === 'cliente') {
            
            // SI ES CLIENTE: Mostramos el botón de añadir al carrito
            echo "<form class='form-carrito' method='POST' action='actions/add_carrito.php'>
                    <input type='hidden' name='id_producto' value='" . $row['ID_PRODUCTO'] . "'>
                    <input type='hidden' name='producto' value='" . htmlspecialchars($row['NOMBRE']) . "'>
                    <input type='hidden' name='precio' value='" . $row['PRECIO'] . "'>
                    <input type='hidden' name='stock' value='" . $row['STOCK'] . "'>
                    <button type='submit' class='btn-carrito'>Añadir al carrito</button>
                  </form>";
                  
        } else {
            
            // SI NO HA INICIADO SESIÓN (o es admin/trabajador): Mostramos un mensaje o botón de aviso
            // (Le pongo un poco de estilo en línea, pero puedes llevarlo a tu carta.css usando la clase 'btn-login-pedido')
            echo "<a href='registro.php' class='btn-login-pedido'>Inicia sesión</a>";
            
        }

        echo "</div>";

        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='mensaje-vacio'>No hay productos disponibles.</p>";
}
?>
</section>

<!-- COLUMNA DERECHA -->
<aside class="columna-lateral">
<?php if ($especial): ?>
    <div class="tarjeta-especialidad">

        <div class="header-especial">
            <h3>DAILY SPECIAL</h3>
            <p class="fechas">Edición Limitada</p>
        </div>

        <div class="seccion-grano">
            <h4>Origen del Grano</h4>
            <p class="origen"><?php echo htmlspecialchars($especial['ORIGEN_GRANO']); ?></p>
            <p class="notas"><i>"<?php echo htmlspecialchars($especial['NOTAS_CATA']); ?>"</i></p>
            <div class="etiqueta"><?php echo htmlspecialchars($especial['TUESTE']); ?></div>
        </div>

        <hr>

        <div class="seccion-metodo">
            <h4>Recomendación de Barista</h4>
            <p class="metodo"><?php echo htmlspecialchars($especial['METODO_FILTRO']); ?></p>
            <p class="desc-filtro"><?php echo htmlspecialchars($especial['DESCRIPCION_FILTRO']); ?></p>
        </div>

        <div class="destacado-seasonal">
            <h4>Bebida de Temporada</h4>
            <p class="seasonal-nombre"><?php echo htmlspecialchars($especial['SEASONAL_NOMBRE']); ?></p>
            <p class="seasonal-desc"><?php echo htmlspecialchars($especial['SEASONAL_DESCRIPCION']); ?></p>
        </div>

    </div>
<?php else: ?>
    <p class="mensaje-vacio">No hay daily special disponible hoy.</p>
<?php endif; ?>
</aside>

<?php $conn->close(); ?>

</main>

<?php include "includes/footer.php"; ?>
<script src="assets/js/script.js"></script>
</body>
</html>
