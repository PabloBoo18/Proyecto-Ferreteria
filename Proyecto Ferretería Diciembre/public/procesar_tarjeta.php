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
    header('Location: catalogo.php');
    exit;
}

// Capturar datos de la tarjeta
$nombre = $_POST['nombre'];
$numero_tarjeta = $_POST['numero_tarjeta'];
$fecha_expiracion = $_POST['fecha_expiracion'];
$cvv = $_POST['cvv'];

// Validar datos básicos de la tarjeta
if (strlen($numero_tarjeta) !== 16 || strlen($cvv) !== 3) {
    echo "<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
                <h3>Error en el pago</h3>
                <p>Los datos de tu tarjeta no son válidos. Por favor, inténtalo de nuevo.</p>
                <a href='pago_tarjeta.php' class='btn btn-secondary'>Volver</a>
            </div>
          </div>";
    exit;
}

try {
    $conn->beginTransaction();

    // Calcular el total
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['cantidad'] * $item['precio'];
    }

    // Insertar el pedido
    $stmt = $conn->prepare('INSERT INTO pedidos (id_usuario, total, metodo_pago, estado) VALUES (:id_usuario, :total, "tarjeta", "pendiente")');
    $stmt->bindParam(':id_usuario', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':total', $total, PDO::PARAM_STR);
    $stmt->execute();

    $id_pedido = $conn->lastInsertId();

    // Insertar los detalles del pedido
    $stmt_detalle = $conn->prepare('INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)');
    foreach ($_SESSION['carrito'] as $id_producto => $item) {
        $stmt_detalle->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
        $stmt_detalle->bindParam(':precio_unitario', $item['precio'], PDO::PARAM_STR);
        $stmt_detalle->execute();
    }

    // Vaciar el carrito
    unset($_SESSION['carrito']);

    $conn->commit();

    // Redirigir a la página de confirmación
    header("Location: confirmacion.php?id=$id_pedido&metodo=tarjeta");
    exit;
} catch (Exception $e) {
    $conn->rollBack();
    echo "<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
                <h3>Error al procesar el pedido</h3>
                <p>Hubo un error al registrar tu pedido: " . $e->getMessage() . "</p>
                <a href='carrito.php' class='btn btn-secondary'>Volver al carrito</a>
            </div>
          </div>";
    exit;
}
?>
