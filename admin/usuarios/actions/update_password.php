<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['superadmin']);

// Recibe los datos del formulario.
$user_id = $_POST['user_id']; // Asegúrate de enviar el ID del usuario desde el formulario.
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$username = $_POST['username'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("UPDATE users SET password = ? WHERE id = ?");

$stmt->bind_param("si", $password, $user_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Contraseña actualizada exitosamente.";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/usuarios/edit.php?username=" . $username);
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(), "/admin/usuarios/edit.php?username=" . $username);
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(), "/admin/usuarios/edit.php?username=" . $username);
}

$stmt->close();
$mydb->close();
