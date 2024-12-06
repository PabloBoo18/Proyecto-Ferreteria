<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el ID del producto está presente en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Producto no encontrado";
    exit;
}

$id_producto = intval($_GET['id']);

// Obtener los detalles del producto de la base de datos
$stmt = $conn->prepare('SELECT * FROM productos WHERE id_producto = :id_producto');
$stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el producto existe
if (!$producto) {
    echo "Producto no encontrado";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
        
        <div>
            <img src="../img/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen del producto" class="img-fluid" style="max-width: 300px; height: auto; float: left; margin-right: 15px;">
        </div>
        
        <div style="clear: both; margin-top: 20px;">
            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
            <p><strong>Precio:</strong> $<?php echo htmlspecialchars($producto['precio']); ?></p>
            <p><strong>Stock:</strong> <?php echo htmlspecialchars($producto['stock']); ?></p>

            <!-- Formulario para añadir al carrito -->
            <form id="addToCartForm">
                <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                <input type="hidden" name="action" value="add_to_cart">
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" class="form-control" name="cantidad" value="1" min="1" max="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                </div>
                <button type="button" class="btn btn-primary" id="addToCartButton">Añadir al carrito</button>
            </form>

            <a href="catalogo.php" class="btn btn-secondary mt-3">Volver al Catálogo</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../js/detalle_producto.js"></script>
</body>
</html>
