<?php
session_start();
// Incluir la base de la URL si lo tienes definido en un archivo de configuración
$base_url = 'http://localhost:3000/XAMPP/htdocs/Proyecto%20Ferretería%20Diciembre/public/';  // Define la base de la URL relativa al servidor
?>

<!-- Incluir Font Awesome para el icono del carrito -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="../css/styles.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
        <img src="<?php echo $base_url; ?>../img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="Logo">
        Ferretería Toledo
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>catalogo.php">Catálogo</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>perfil.php">Perfil</a></li>

                <!-- Mostrar "Gestionar productos" solo si el usuario es administrador -->
                <?php if ($_SESSION['rol'] === 'administrador'): ?>
                    <li class="nav-item"><a class="nav-link" href="../admin/gestionar_productos.php">Gestionar Productos</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/admin_pedidos.php">Administrar Pedidos</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>../admin/crear_admin.php">Registro Administrador</a></li>
                <?php endif; ?>

                <!-- Icono del carrito y el número de productos -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>carrito.php">
                        <i class="fas fa-shopping-cart"></i>
                        Carrito
                         <span id="cart-count" class="badge badge-pill badge-danger">
                            <?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>
                        </span> 
                       
                    </a>
                </li>

                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>logout.php">Cerrar Sesión</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>registro.php">Registro</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>