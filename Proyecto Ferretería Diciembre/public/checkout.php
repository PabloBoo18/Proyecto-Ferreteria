<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar si el carrito tiene productos
if (empty($_SESSION['carrito'])) {
    echo "<div class='container mt-5'><p>No hay productos en el carrito para realizar el pedido.</p>";
    echo '<a href="catalogo.php" class="btn btn-secondary">Volver al Catálogo</a></div>';
    exit;
}

// Verificar si se ha seleccionado un método de pago
if (!isset($_POST['metodo_pago']) || empty($_POST['metodo_pago'])) {
    echo "<div class='container mt-5'><p>Método de pago no seleccionado.</p>";
    echo '<a href="pago_metodo.php" class="btn btn-secondary">Volver</a></div>';
    exit;
}

$metodo_pago = $_POST['metodo_pago'];

try {
    $conn->beginTransaction();

    // Calcular el total
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['cantidad'] * $item['precio'];
    }

    // Insertar el pedido
    $stmt = $conn->prepare('INSERT INTO pedidos (id_usuario, total, metodo_pago, estado) VALUES (:id_usuario, :total, :metodo_pago, "pendiente")');
    $stmt->bindParam(':id_usuario', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':total', $total, PDO::PARAM_STR);
    $stmt->bindParam(':metodo_pago', $metodo_pago, PDO::PARAM_STR);
    $stmt->execute();

    $id_pedido = $conn->lastInsertId();

    // Insertar los detalles del pedido y actualizar el stock
    $stmt_detalle = $conn->prepare('INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)');
    $stmt_stock = $conn->prepare('UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id_producto AND stock >= :cantidad');

    foreach ($_SESSION['carrito'] as $id_producto => $item) {
        // Verificar si hay stock suficiente
        $stmt_stock->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt_stock->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
        $stmt_stock->execute();

        if ($stmt_stock->rowCount() == 0) {
            throw new Exception("No hay suficiente stock para el producto ID: $id_producto");
        }

        // Insertar los detalles del pedido
        $stmt_detalle->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
        $stmt_detalle->bindParam(':precio_unitario', $item['precio'], PDO::PARAM_STR);
        $stmt_detalle->execute();
    }

    // Vaciar el carrito
    unset($_SESSION['carrito']);

    $conn->commit();

    // Redirigir a la confirmación
    header("Location: confirmacion.php?id=$id_pedido&metodo=$metodo_pago");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
                <h3>Error al procesar el pedido</h3>
                <p>Hubo un problema: " . $e->getMessage() . "</p>
                <a href='carrito.php' class='btn btn-secondary'>Volver al Carrito</a>
            </div>
          </div>";
    exit;
}
?>
