<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/roles.php';

// iniciar sesión y verificar autorización
session_start();

verifyRole('superadmin');

// Recibe los datos del formulario.
$business_id = $_POST['business_id']; // Asegúrate de enviar el ID del negocio desde el formulario.
$name = $_POST['name'];
$is_active = isset($_POST['is_active']) ? 1 : 0;
$expiration_date = $_POST['expiration_date'];
$old_photo = $_POST['old_photo'];

$upload_system_dir = "../../../uploads/businesses/"; // Asegúrate de tener este directorio creado y con permisos de escritura
$upload_url_dir = "/uploads/businesses/";

// Manejar la subida del logo
try {
    if (isset($_FILES['logo'])) {
        $logo_path = upload_photo($_FILES['logo'], $upload_system_dir, $upload_url_dir);
    }
} catch (Exception $e) {
    handle_form_error($e->getMessage(), $_POST, "/admin/empresas/edit.php?id=" . $business_id);
}

$logo_path = $logo_path ? $logo_path : $old_photo;

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    UPDATE businesses SET name = ?, logo = ?, is_active = ?, expiration_date = ?
    WHERE id = ?
");

$stmt->bind_param("ssiii", $name, $logo_path, $is_active, $logo, $business_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Empresa actualizada exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/empresas");
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
        ), "/admin/empresas/edit.php?id=" . $business_id);
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(
        'name' => $name,
    ), "/admin/empresas/edit.php?id=" . $business_id);
}

$stmt->close();
$mydb->close();
