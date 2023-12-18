<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();
verifyRoles(['admin']);

// Recibir los datos del formulario
$document_id = $_POST['document_id'];

$upload_system_dir = "../../../../uploads/files/"; // Directorio donde guardar los archivos
$upload_url_dir = "/uploads/files/";

if ($_FILES['file']['size'] < FILE_MAX_SIZE) {
    // Obtener detalles del archivo
    $file_name = $_FILES['file']['name'];
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_error = $_FILES['file']['error'];
    $file_ext = strtolower(end(explode('.', $file_name)));

    // Generar un nombre único para el archivo para evitar sobreescrituras
    $stored_file_name = uniqid('', true) . '.' . $file_ext;

    // Ruta completa del archivo en el servidor
    $file_system_path = $upload_system_dir . $stored_file_name;

    // Verificar errores
    if ($file_error === 0) {
        if (move_uploaded_file($file_tmp_name, $file_system_path)) {
            // Ruta de acceso al archivo para acceso URL
            $file_url_path = $upload_url_dir . $stored_file_name;

            // Actualizar registro en la base de datos
            $stmt = $mydb->prepare("
                UPDATE file_docs 
                SET file_path = ?, file_name = ?, file_weight = ?, file_extension = ? 
                WHERE id = ?
            ");
            $stmt->bind_param("ssisi", $file_url_path, $file_name, $file_size, $file_ext, $document_id);

            try {
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Documento actualizado exitosamente";
                    header("Location: " . BASE_URL . "/business/contenido/documentos/edit_file.php?document_id=" . $document_id);
                    exit;
                } else {
                    handle_form_error("Error: " . $stmt->error, [], "/business/contenido/documentos/edit_file.php?document_id=" . $document_id);
                }
            } catch (Exception $e) {
                handle_form_error("Error: " . $e->getMessage(), [], "/business/contenido/documentos/edit_file.php?document_id=" . $document_id);
            }

            $stmt->close();
            $mydb->close();
        } else {
            handle_form_error("Error: No se pudo guardar el archivo en el servidor.", [], "/business/contenido/documentos/edit_file.php?document_id=" . $document_id);
        }
    } else {
        handle_form_error("Error: Hubo un problema con la subida del archivo.", [], "/business/contenido/documentos/edit_file.php?document_id=" . $document_id);
    }
} else {
    $maxSizeMB = FILE_MAX_SIZE / (1024 * 1024); // Convertir a MB
    handle_form_error("Error: El peso máximo es " . $maxSizeMB . " MB", [], "/business/contenido/documentos/edit_file.php?document_id=" . $document_id);
}

?>
