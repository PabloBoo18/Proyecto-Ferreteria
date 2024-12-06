<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar que se recibió un ID de pedido y método de pago
if (!isset($_GET['id']) || !isset($_GET['metodo_pago'])) {
    echo "<div class='container mt-5'><p>Información incompleta para mostrar la confirmación.</p>";
    echo '<a href="catalogo.php" class="btn btn-secondary">Volver al Catálogo</a></div>';
    exit;
}

$id_pedido = intval($_GET['id']);
$metodo_pago = $_GET['metodo_pago'];

// Obtener detalles del pedido
$stmt = $conn->prepare('SELECT total, fecha FROM pedidos WHERE id_pedido = :id_pedido');
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->execute();

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo "<div class='container mt-5'><p>Pedido no encontrado.</p>";
    echo '<a href="catalogo.php" class="btn btn-secondary">Volver al Catálogo</a></div>';
    exit;
}

// Mensaje según el método de pago
$mensaje = '';
$datos_transferencia = '';
switch ($metodo_pago) {
    case 'tarjeta':
        $mensaje = "Tu pago con tarjeta se ha procesado correctamente.";
        break;
    case 'transferencia':
        $mensaje = "Por favor, realiza la transferencia bancaria utilizando los siguientes datos:";
        $datos_transferencia = "
            <ul>
                <li><strong>Titular de la cuenta:</strong> Ferretería Toledo</li>
                <li><strong>Banco:</strong> Banco Ejemplo</li>
                <li><strong>Número de cuenta:</strong> 1234567890123456</li>
                <li><strong>IBAN:</strong> ES1234567890123456789012</li>
                <li><strong>Concepto:</strong> Pedido #$id_pedido</li>
            </ul>
            <p>Por favor, asegúrate de incluir el número de pedido en el concepto de la transferencia.</p>
        ";
        break;
    case 'contrareembolso':
        $mensaje = "Por favor, realiza el pago cuando recibas el pedido.";
        break;
    default:
        $mensaje = "Gracias por tu compra.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Confirmación de Pago</h2>
        <div class="alert alert-success" role="alert">
            <p><strong>Pedido ID:</strong> <?php echo $id_pedido; ?></p>
            <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 2); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></p>
            <p><?php echo $mensaje; ?></p>
            <?php if (!empty($datos_transferencia)): ?>
                <div class="alert alert-info mt-3">
                    <h5>Datos para Transferencia Bancaria</h5>
                    <?php echo $datos_transferencia; ?>
                </div>
            <?php endif; ?>
        </div>
        <a href="perfil.php" class="btn btn-primary">Volver al Perfil</a>
    </div>
</body>
</html>
