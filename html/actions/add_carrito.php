<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Recogemos los datos EXACTOS que envías desde tu formulario
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['producto']; // Tu input se llama 'producto'
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']); // ¡Genial que pases el stock!
    
    // Como tu botón no tiene input de cantidad, cada clic suma 1
    $cantidad_a_anadir = 1;

    // 2. Si la "caja" del carrito no existe, la creamos vacía
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // 3. Calculamos cuántos de este producto ya tiene el cliente en su carrito
    $cantidad_actual_en_carrito = 0;
    if (isset($_SESSION['carrito'][$id_producto])) {
        $cantidad_actual_en_carrito = $_SESSION['carrito'][$id_producto]['cantidad'];
    }

    // 4. EL TOQUE PROFESIONAL: Validación de Stock
    // ¿Si sumo 1 más, me paso del stock que hay en la base de datos?
    if (($cantidad_actual_en_carrito + $cantidad_a_anadir) <= $stock) {
        
        // Hay stock suficiente, ¡lo añadimos o sumamos!
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad_a_anadir;
        } else {
            // Es la primera vez que añade este producto
            $_SESSION['carrito'][$id_producto] = [
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => $cantidad_a_anadir
            ];
        }
        
    } else {
        // OPCIONAL: Si quieres, puedes guardar un mensaje de error en sesión
        // para mostrar un pop-up en la carta diciendo "¡Ups, no queda más stock!"
        $_SESSION['error_carrito'] = "Has alcanzado el límite de stock de " . $nombre;
    }

    // 5. Devolvemos al usuario a la carta súper rápido (ni se enterará de que ha cambiado de página)
    header("Location: ../carta.php");
    exit();

} else {
    // Si alguien listillo intenta escribir la URL a mano, lo rebotamos
    header("Location: ../carta.php");
    exit();
}
?>