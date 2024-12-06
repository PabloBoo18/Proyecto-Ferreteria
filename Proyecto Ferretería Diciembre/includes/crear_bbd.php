<?php
// Datos de conexión a la base de datos
$servername = "localhost:3307";  // Cambia si es necesario
$username = "pablo";       // Nombre de usuario de MySQL
$password = "";    // Contraseña de MySQL
$dbname = "ferreteria";         // Nombre de la base de datos que quieres crear

try {
    // Crear conexión a MySQL
    $conn = new PDO("mysql:host=$servername", $username, $password);
    // Configurar PDO para que muestre excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear la base de datos si no existe
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->exec($sql);
    echo "Base de datos creada exitosamente<br>";

    // Usar la base de datos
    $conn->exec("USE $dbname");

    // Crear tabla usuarios
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        correo VARCHAR(100) UNIQUE NOT NULL,
        contraseña VARCHAR(255) NOT NULL,
        telefono VARCHAR(15),
        direccion VARCHAR(255),
        ciudad VARCHAR(100),
        codigo_postal VARCHAR(10),
        rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente'
    )";
    $conn->exec($sql);
    echo "Tabla usuarios creada exitosamente<br>";

    // Crear tabla productos
    $sql = "CREATE TABLE IF NOT EXISTS productos (
        id_producto INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        imagen VARCHAR(255),
        precio DECIMAL(10, 2),
        stock INT
    )";
    $conn->exec($sql);
    echo "Tabla productos creada exitosamente<br>";

    // Crear tabla pedidos
    $sql = "CREATE TABLE IF NOT EXISTS pedidos (
        id_pedido INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        estado ENUM('pendiente', 'completado', 'cancelado') NOT NULL DEFAULT 'pendiente',
        metodo_pago VARCHAR(50),
        total DECIMAL(10, 2),
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    )";
    $conn->exec($sql);
    echo "Tabla pedidos creada exitosamente<br>";

    // Crear tabla detalles_pedido
    $sql = "CREATE TABLE IF NOT EXISTS detalles_pedido (
        id_detalle INT AUTO_INCREMENT PRIMARY KEY,
        id_pedido INT NOT NULL,
        id_producto INT NOT NULL,
        cantidad INT,
        precio_unitario DECIMAL(10, 2),
        FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
        FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
    )";
    $conn->exec($sql);
    echo "Tabla detalles_pedido creada exitosamente<br>";

    // Crear un usuario administrador predeterminado
    $admin_name = 'admin';
    $admin_email = 'admin@ferreteria.com';
    $admin_password = 'admin';
    $admin_role = 'admin';

    // Verificar si ya existe un administrador
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = :email");
    $stmt->bindParam(':email', $admin_email);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // Insertar el administrador si no existe
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (:name, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $admin_name);
        $stmt->bindParam(':email', $admin_email);
        $stmt->bindParam(':password', $admin_password);
        $stmt->bindParam(':role', $admin_role);
        $stmt->execute();
        echo "Usuario administrador creado con éxito<br>";
    } else {
        echo "El usuario administrador ya existe<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>
