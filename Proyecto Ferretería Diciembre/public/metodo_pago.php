<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el carrito tiene productos
if (empty($_SESSION['carrito'])) {
    echo "<div class='container mt-5'><p>No hay productos en el carrito.</p>";
    echo '<a href="catalogo.php" class="btn btn-secondary">Volver al Catálogo</a></div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Método de Pago</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Seleccionar Método de Pago</h2>
        <form action="checkout.php" method="POST">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" required>
                <label class="form-check-label" for="tarjeta">Tarjeta</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia">
                <label class="form-check-label" for="transferencia">Transferencia</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" id="contrareembolso" value="contrareembolso">
                <label class="form-check-label" for="contrareembolso">Contrareembolso</label>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Proceder al Pago</button>
        </form>
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