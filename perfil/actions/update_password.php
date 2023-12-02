<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyAuthentication();

// Recibe los datos del formulario.
$user_id = $_SESSION['user_id']; 
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("UPDATE users SET password = ? WHERE id = ?");

$stmt->bind_param("si", $password, $user_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Contraseña actualizada exitosamente.";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/perfil");
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(), "/perfil");
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(), "/perfil");
}

$stmt->close();
$mydb->close();
