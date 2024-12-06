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

// Eliminar el producto de la base de datos
$stmt = $conn->prepare('DELETE FROM productos WHERE id_producto = :id_producto');
$stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);

if ($stmt->execute()) {
    // Redirigir a la gestión de productos con un mensaje de éxito
    header('Location: gestionar_productos.php?mensaje=eliminado');
    exit;
} else {
    echo "Error al eliminar el producto.";
}
?>