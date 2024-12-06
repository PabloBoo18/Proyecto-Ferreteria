<?php
$servername = "localhost:3307"; // Cambia el puerto si es necesario
$username = "root";              // Usuario de MySQL
$password = "";                  // Contraseña de MySQL
$dbname = "ferreteria";          // Nombre de la base de datos

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configurar PDO para manejar errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
