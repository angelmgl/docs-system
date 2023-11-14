<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/roles.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['superadmin']);

// Recibe los datos del formulario.
$business_id = $_POST["business_id"];
$user_id = $_POST["user_id"];
$role_id = $_POST["role_id"];

$stmt = $mydb->prepare("INSERT INTO roles_businesses (role_id, business_id, user_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $role_id, $business_id, $user_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario agregado exitosamente";

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
