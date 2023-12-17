<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

// Recibe los datos del formulario.
$user_id = $_POST['user_id']; // Asegúrate de enviar el ID del usuario desde el formulario.
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$username = $_POST['username'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("UPDATE users SET password = ? WHERE id = ? AND business_id = ?");

$stmt->bind_param("sii", $password, $user_id, $my_business);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Contraseña actualizada exitosamente.";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/business/usuarios/edit.php?username=" . $username);
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(), "/business/usuarios/edit.php?username=" . $username);
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(), "/business/usuarios/edit.php?username=" . $username);
}

$stmt->close();
$mydb->close();
