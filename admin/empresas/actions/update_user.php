<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibe los datos del formulario.
$business_id = $_POST["business_id"];
$user_id = $_POST["user_id"];
$role = $_POST["role"];

$stmt = $mydb->prepare("
UPDATE users
SET role = ?
WHERE business_id = ? AND id = ?;
");
$stmt->bind_param("sii", $role, $business_id, $user_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario modificado exitosamente";

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
