<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibe los datos del formulario.
$user_id = $_POST["user_id"];
$business_id = $_POST["business_id"];

$stmt = $mydb->prepare("
    UPDATE users
    SET business_id = NULL
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);


try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario eliminado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/empresas/edit.php?id=" . $business_id);
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(), "/admin/empresas/add-user.php?business_id=" . $business_id);
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(), "/admin/empresas/add-user.php?business_id=" . $business_id);
}

$stmt->close();
$mydb->close();
