<?php
include '../includes/db.php'; // Archivo de conexión a la base de datos
include 'navbar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario existe
    $stmt = $conn->prepare('SELECT id_usuario, contraseña, rol FROM usuarios WHERE correo = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Si encontramos al usuario
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener datos del usuario
        
        // Verificar la contraseña
        if (password_verify($password, $user['contraseña'])) {
            // Crear sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['rol'] = $user['rol'];

            // Redirigir al perfil o al panel de administrador
            if ($user['rol'] === 'administrador') {
                header('Location: admin.php');
            } else {
                header('Location: perfil.php');
            }
            exit;
        } else {
            echo 'Contraseña incorrecta';
        }
    } else {
        echo 'No existe un usuario con ese correo';
    }
}
?>
