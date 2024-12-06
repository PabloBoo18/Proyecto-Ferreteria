<?php
include '../includes/db.php';
include 'navbar.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener la información completa del usuario
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT nombre, correo, direccion, telefono, ciudad, codigo_postal FROM usuarios WHERE id_usuario = :id_usuario');
$stmt->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el usuario existe
if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Perfil del Usuario</h2>

        <!-- Mostrar mensaje de éxito si se actualizó correctamente -->
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'actualizado'): ?>
            <div class="alert alert-success">Datos actualizados correctamente.</div>
        <?php endif; ?>

        <!-- Datos básicos del usuario -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Datos personales</h5>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['direccion'] ?? 'No especificada'); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono'] ?? 'No especificado'); ?></p>
                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($usuario['ciudad'] ?? 'No especificada'); ?></p>
                <p><strong>Código Postal:</strong> <?php echo htmlspecialchars($usuario['codigo_postal'] ?? 'No especificado'); ?></p>
                
                <!-- Botones para editar información y ver pedidos -->
                <a href="editar_datos.php" class="btn btn-info mt-3">Editar Información</a>
                <a href="mis_pedidos.php" class="btn btn-primary mt-3">Mis Pedidos</a>
            </div>
        </div>
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
