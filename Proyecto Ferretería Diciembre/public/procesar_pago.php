<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar si se recibió el método de pago
if (!isset($_POST['metodo_pago'])) {
    header('Location: metodo_pago.php');
    exit;
}

$metodo_pago = $_POST['metodo_pago'];
$user_id = $_SESSION['user_id'];

// Aquí puedes procesar los datos de pago según el método seleccionado
if ($metodo_pago == 'tarjeta') {
    // Validar y guardar datos de la tarjeta si es necesario
} elseif ($metodo_pago == 'contrareembolso') {
    // Procesar pedido sin datos adicionales
} elseif ($metodo_pago == 'transferencia') {
    // Registrar la orden como pendiente hasta recibir el pago
}

// Redirigir a la página de confirmación
header('Location: checkout.php?mensaje=exito');
exit;
?>
