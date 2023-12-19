<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

// Recibe los datos del formulario.
$business_id = $_SESSION['business_id']; // Asegúrate de enviar el ID del negocio desde el formulario.
$name = $_POST['name'];
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
    UPDATE businesses SET name = ?, logo = ?
    WHERE id = ?
");

$stmt->bind_param("ssi", $name, $logo_path, $business_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Empresa actualizada exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/business/empresa");
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
        ), "/business/empresa/edit.php");
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(
        'name' => $name,
    ), "/business/empresa/edit.php");
}

$stmt->close();
$mydb->close();
