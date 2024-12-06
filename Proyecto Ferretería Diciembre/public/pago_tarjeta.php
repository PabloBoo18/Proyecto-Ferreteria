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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar con Tarjeta</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pagar con Tarjeta</h2>
        <form action="procesar_tarjeta.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Titular</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="numero_tarjeta">Número de Tarjeta</label>
                <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" maxlength="16" required>
            </div>
            <div class="form-group">
                <label for="fecha_expiracion">Fecha de Expiración</label>
                <input type="text" class="form-control" id="fecha_expiracion" name="fecha_expiracion" placeholder="MM/AA" maxlength="5" required>
            </div>
            <div class="form-group">
                <label for="cvv">CVV</label>
                <input type="password" class="form-control" id="cvv" name="cvv" maxlength="3" required>
            </div>
            <button type="submit" class="btn btn-primary">Pagar</button>
        </form>
    </div>
</body>
</html>
