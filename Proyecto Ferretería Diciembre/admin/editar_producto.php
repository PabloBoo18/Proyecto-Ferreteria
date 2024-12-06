<?php
include '../includes/db.php';
include '../public/navbar.php'; // Ruta ajustada para incluir el navbar

// Verificar si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo "Acceso denegado.";
    exit;
}

// Verificar si se recibió el ID del producto
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID del producto no especificado.";
    exit;
}

$id_producto = intval($_GET['id']); // Convertir a entero por seguridad

// Obtener los datos actuales del producto
$stmt = $conn->prepare('SELECT * FROM productos WHERE id_producto = :id_producto');
$stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el producto existe
if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

$error = ''; // Variable para manejar errores

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen_actual = $producto['imagen']; // Imagen actual en caso de que no se suba una nueva
    $imagen = $_FILES['imagen'];

    // Validar los campos
    if (empty($nombre) || empty($descripcion) || empty($precio) || empty($stock)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        // Manejar la subida de una nueva imagen (opcional)
        if (!empty($imagen['name'])) {
            $ruta_imagen = '../img/' . basename($imagen['name']);
            if (move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
                $imagen_actual = $imagen['name']; // Actualizar la imagen solo si se subió correctamente
            } else {
                $error = 'Error al subir la nueva imagen. Por favor, inténtalo de nuevo.';
            }
        }

        // Actualizar el producto en la base de datos
        if (empty($error)) {
            $stmt = $conn->prepare('UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen = :imagen WHERE id_producto = :id_producto');
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':imagen', $imagen_actual);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->execute();

            // Redirigir a la gestión de productos
            header('Location: gestionar_productos.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Producto</h2>

        <!-- Mostrar errores si existen -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulario para editar un producto -->
        <form action="editar_producto.php?id=<?php echo $id_producto; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen</label>
                <input type="file" class="form-control-file" id="imagen" name="imagen">
                <small>Si no seleccionas una nueva imagen, se conservará la actual.</small>
                <div class="mt-2">
                    <img src="../img/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen actual" style="width: 100px; height: auto;">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="gestionar_productos.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
