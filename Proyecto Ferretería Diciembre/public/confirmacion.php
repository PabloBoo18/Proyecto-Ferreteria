<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si se ha recibido el ID del pedido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Pedido no encontrado.";
    exit;
}

$id_pedido = intval($_GET['id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación del Pedido</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pedido Confirmado</h2>
        <p>Su pedido ha sido procesado correctamente.</p>
        <p>El número de su pedido es: <strong><?php echo htmlspecialchars($id_pedido); ?></strong></p>
        <a href="perfil.php" class="btn btn-primary">Volver al Perfil</a>
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

</body>
</html>
