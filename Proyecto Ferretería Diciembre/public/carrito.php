<?php
include '../includes/db.php';
include 'navbar.php';

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? null;

    try {
        if ($action === 'add_to_cart') {
            $id_producto = intval($_POST['id_producto']);
            $cantidad = intval($_POST['cantidad']);

            // Obtener los datos del producto desde la base de datos
            $stmt = $conn->prepare('SELECT nombre, precio, stock FROM productos WHERE id_producto = :id_producto');
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto && $cantidad > 0) {
                // Verificar stock
                if ($cantidad > $producto['stock']) {
                    echo json_encode(['error' => 'No hay suficiente stock disponible.']);
                    exit;
                }

                // Si el producto ya está en el carrito, actualizar la cantidad
                if (isset($_SESSION['carrito'][$id_producto])) {
                    $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;

                    // Verificar nuevamente el stock
                    if ($_SESSION['carrito'][$id_producto]['cantidad'] > $producto['stock']) {
                        $_SESSION['carrito'][$id_producto]['cantidad'] = $producto['stock'];
                    }
                } else {
                    // Agregar nuevo producto al carrito
                    $_SESSION['carrito'][$id_producto] = [
                        'nombre' => $producto['nombre'],
                        'precio' => $producto['precio'],
                        'cantidad' => $cantidad,
                        'stock' => $producto['stock']
                    ];
                }

                // Reducir el stock del producto en la base de datos
                $nuevo_stock = $producto['stock'] - $cantidad;
                $stmt_update = $conn->prepare('UPDATE productos SET stock = :stock WHERE id_producto = :id_producto');
                $stmt_update->bindParam(':stock', $nuevo_stock, PDO::PARAM_INT);
                $stmt_update->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $stmt_update->execute();

                // Calcular el total de productos en el carrito
                $total_productos = 0;
                foreach ($_SESSION['carrito'] as $producto_carrito) {
                    $total_productos += $producto_carrito['cantidad'];
                }

                echo json_encode(['success' => 'Producto agregado al carrito.', 'cart_count' => $total_productos]);
                exit;
            } else {
                echo json_encode(['error' => 'Producto no encontrado o cantidad inválida.']);
                exit;
            }
        }

        if ($action === 'remove_from_cart') {
            $id_producto = intval($_POST['id_producto']);
            if (isset($_SESSION['carrito'][$id_producto])) {
                // Restaurar el stock en la base de datos
                $cantidad = $_SESSION['carrito'][$id_producto]['cantidad'];
                $stmt = $conn->prepare('UPDATE productos SET stock = stock + :cantidad WHERE id_producto = :id_producto');
                $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $stmt->execute();

                // Eliminar el producto del carrito
                unset($_SESSION['carrito'][$id_producto]);
            }

            // Calcular el total de productos en el carrito
            $total_productos = 0;
            foreach ($_SESSION['carrito'] as $producto_carrito) {
                $total_productos += $producto_carrito['cantidad'];
            }

            echo json_encode(['success' => 'Producto eliminado del carrito.', 'cart_count' => $total_productos]);
            exit;
        }

        if ($action === 'update_quantity') {
            $id_producto = intval($_POST['id_producto']);
            $cantidad = intval($_POST['cantidad']);

            if (isset($_SESSION['carrito'][$id_producto]) && $cantidad > 0) {
                $producto = $_SESSION['carrito'][$id_producto];

                // Ajustar el stock en la base de datos
                $diferencia_cantidad = $cantidad - $producto['cantidad'];
                if ($diferencia_cantidad > 0) {
                    $stmt = $conn->prepare('UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id_producto');
                } else {
                    $stmt = $conn->prepare('UPDATE productos SET stock = stock + :cantidad WHERE id_producto = :id_producto');
                    $diferencia_cantidad = abs($diferencia_cantidad);
                }

                $stmt->bindParam(':cantidad', $diferencia_cantidad, PDO::PARAM_INT);
                $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $stmt->execute();

                // Actualizar cantidad en el carrito
                $_SESSION['carrito'][$id_producto]['cantidad'] = $cantidad;

                // Calcular el total de productos en el carrito
                $total_productos = 0;
                foreach ($_SESSION['carrito'] as $producto_carrito) {
                    $total_productos += $producto_carrito['cantidad'];
                }

                echo json_encode(['success' => 'Cantidad actualizada correctamente.', 'cart_count' => $total_productos]);
                exit;
            } else {
                echo json_encode(['error' => 'Producto no encontrado en el carrito o cantidad inválida.']);
                exit;
            }
        }

        echo json_encode(['error' => 'Acción no válida.']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        exit;
    }
}

// Mostrar el carrito
$carrito = $_SESSION['carrito'];
$total = 0;
foreach ($carrito as $item) {
    $total += $item['cantidad'] * $item['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Carrito de Compras</h2>

        <?php if (!empty($carrito)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $id_producto => $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                    <td>
                        <input type="number" class="form-control quantity-input" value="<?php echo $item['cantidad']; ?>" min="1"
                            max="<?php echo $item['stock']; ?>" data-product-id="<?php echo $id_producto; ?>">
                    </td>
                    <td>$<?php echo number_format($item['precio'], 2); ?></td>
                    <td>$<?php echo number_format($item['cantidad'] * $item['precio'], 2); ?></td>
                    <td>
                    <button class="btn btn-danger btn-sm remove-btn" data-product-id="<?php echo $id_producto; ?>">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4>Total: $<?php echo number_format($total, 2); ?></h4>
        <?php else: ?>
        <p>No hay productos en el carrito.</p>
        <?php endif; ?>

        <a href="catalogo.php" class="btn btn-secondary">Volver al Catálogo</a>
        <a href="metodo_pago.php" class="btn btn-primary">Proceder al Pago</a>
    </div>

    <footer class="bg-light text-center text-lg-start mt-5">
    <div class="container p-4">
        <!-- Sección de enlaces -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Información</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="#" class="text-dark">Condiciones</a></li>
                    <li><a href="#" class="text-dark">Condiciones de envío</a></li>
                    <li><a href="#" class="text-dark">Política de devoluciones</a></li>
                    <li><a href="#" class="text-dark">Horario</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Su Cuenta</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="#" class="text-dark">Información personal</a></li>
                    <li><a href="#" class="text-dark">Pedidos</a></li>
                    <li><a href="#" class="text-dark">Facturas por abono</a></li>
                    <li><a href="#" class="text-dark">Cupones de descuento</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Información de la tienda</h5>
                <ul class="list-unstyled mb-0">
                    <li><p class="text-dark mb-0">Boo, Aller</p></li>
                    <li><p class="text-dark mb-0">33675, Asturias</p></li>
                    <li><p class="text-dark mb-0">Tel: 620103131</p></li>
                    <li><p class="text-dark mb-0">Email: pablosg85@educastur.es</p></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Newsletter</h5>
                <form>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Tu correo electrónico" />
                    </div>
                    <button class="btn btn-primary btn-block">Suscribirse</button>
                </form>
            </div>
        </div>
    </div>
    <div class="text-center p-3 bg-dark text-white">
        &copy; <?php echo date("Y"); ?> Ferretería Toledo. Todos los derechos reservados.
    </div>
</footer>
    <script src="../js/carrito.js"></script>
</body>

</html>