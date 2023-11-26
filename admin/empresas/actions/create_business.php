<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibir los datos del formulario
$name = $_POST['name'];
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Iniciar la variable $logo_path con NULL
$logo_path = NULL;

$upload_system_dir = "../../../uploads/businesses/"; // Asegúrate de tener este directorio creado y con permisos de escritura
$upload_url_dir = "/uploads/businesses/";

// Manejar la subida del logo
try {
    if (isset($_FILES['logo'])) {
        $logo_path = upload_photo($_FILES['logo'], $upload_system_dir, $upload_url_dir);
    }
} catch (Exception $e) {
    handle_form_error($e->getMessage(), $_POST, "/admin/empresas/add.php");
}

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    INSERT INTO businesses (name, logo, is_active) 
    VALUES (?, ?, ?)
");

$stmt->bind_param("ssi", $name, $logo_path, $is_active);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Empresa agregada exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/empresas");
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
        ), "/admin/empresas/add.php");
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $e->getMessage(), array(
        'name' => $name,
    ), "/admin/empresas/add.php");
}

$stmt->close();
$mydb->close();
