<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar si se ha recibido un ID de pedido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('Pedido no encontrado.');
            window.location.href = 'mis_pedidos.php';
          </script>";
    exit;
}

$id_pedido = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verificar que el pedido pertenece al usuario logueado
$stmt = $conn->prepare('SELECT * FROM pedidos WHERE id_pedido = :id_pedido AND id_usuario = :id_usuario');
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
$stmt->execute();

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el pedido no pertenece al usuario o no existe
if (!$pedido) {
    echo "<script>
            alert('Pedido no encontrado o acceso denegado.');
            window.location.href = 'mis_pedidos.php';
          </script>";
    exit;
}

// Obtener los productos del pedido
$stmt = $conn->prepare('
    SELECT p.nombre, dp.cantidad, dp.precio_unitario
    FROM detalles_pedido dp
    JOIN productos p ON dp.id_producto = p.id_producto
    WHERE dp.id_pedido = :id_pedido
');
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cancelar el pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelar_pedido'])) {
    $stmt = $conn->prepare('UPDATE pedidos SET estado = "cancelado" WHERE id_pedido = :id_pedido AND id_usuario = :id_usuario AND estado = "pendiente"');
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<script>
                alert('Pedido cancelado con éxito.');
                window.location.href = 'mis_pedidos.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('No se pudo cancelar el pedido. Quizás ya está procesado.');
                window.location.href = 'mis_pedidos.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Detalles del Pedido</h2>

        <!-- Información del pedido -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Información del Pedido</h5>
                <p><strong>ID Pedido:</strong> <?php echo htmlspecialchars($pedido['id_pedido']); ?></p>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 2); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($pedido['estado']); ?></p>
            </div>
        </div>

        <!-- Productos del pedido -->
        <h5>Productos del Pedido</h5>
        <?php if (!empty($productos)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo $producto['cantidad']; ?></td>
                            <td>$<?php echo number_format($producto['precio_unitario'], 2); ?></td>
                            <td>$<?php echo number_format($producto['cantidad'] * $producto['precio_unitario'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay productos en este pedido.</p>
        <?php endif; ?>

        <!-- Botón para cancelar el pedido si está pendiente -->
        <?php if ($pedido['estado'] === 'pendiente'): ?>
            <form method="POST">
                <button type="submit" name="cancelar_pedido" class="btn btn-danger mt-3">Cancelar Pedido</button>
            </form>
        <?php endif; ?>

        <a href="mis_pedidos.php" class="btn btn-secondary mt-3">Volver a Mis Pedidos</a>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
