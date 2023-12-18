<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();
verifyRoles(['admin']);

// Recibir los datos del formulario
$name = $_POST['name'];
$description = $_POST['description'];
$category_id = $_POST['category_id'];

$upload_system_dir = "../../../../uploads/files/"; // Directorio donde guardar los archivos
$upload_url_dir = "/uploads/files/";

if ($_FILES['file']['size'] < FILE_MAX_SIZE) {
    // Obtener detalles del archivo
    $file_name = $_FILES['file']['name'];
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_error = $_FILES['file']['error'];
    $file_type = $_FILES['file']['type'];

    // Extraer la extensión del archivo
    $file_ext = strtolower(end(explode('.', $file_name)));

    // Generar un nombre único para el archivo para evitar sobreescrituras
    $stored_file_name = uniqid('', true) . '.' . $file_ext;

    // Ruta completa del archivo en el servidor
    $file_system_path = $upload_system_dir . $stored_file_name;

    // Verificar errores
    if ($file_error === 0) {
        // Intentar mover el archivo al directorio de destino
        if (move_uploaded_file($file_tmp_name, $file_system_path)) {
            // Ruta de acceso al archivo para acceso URL
            $file_url_path = $upload_url_dir . $stored_file_name;

            // Conexión a la base de datos y preparación de la consulta.
            $stmt = $mydb->prepare("
                INSERT INTO file_docs (name, description, category_id, file_path, file_name, file_weight, file_extension) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("ssissis", $name, $description, $category_id, $file_url_path, $file_name, $file_size, $file_ext);

            try {
                // Intenta ejecutar la consulta
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Documento creado exitosamente";

                    // Cerrar la sentencia y la conexión antes de redirigir
                    $stmt->close();
                    $mydb->close();

                    header("Location: " . BASE_URL . "/business/contenido/categorias/?category_id=" . $category_id);
                    exit;
                } else {
                    // Si hay un error, lo manejamos
                    handle_form_error("Error: " . $stmt->error, array(
                        'name' => $name,
                        'description' => $description
                    ), "/business/contenido/documentos/add_html.php?category_id=" . $category_id);
                }
            } catch (Exception $e) {
                // Esto atrapará cualquier excepción o error fatal que ocurra
                handle_form_error("Error: " . $stmt->error, array(
                    'name' => $name,
                    'description' => $description
                ), "/business/contenido/documentos/add_html.php?category_id=" . $category_id);
            }

            $stmt->close();
            $mydb->close();
        } else {
            handle_form_error("Error: No se pudo guardar el archivo en el servidor.", array(
                'name' => $name,
                'description' => $description
            ), "/business/contenido/documentos/add_file.php?category_id=" . $category_id);
        }
    } else {
        handle_form_error("Error: Hubo un problema con la subida del archivo.", array(
            'name' => $name,
            'description' => $description
        ), "/business/contenido/documentos/add_file.php?category_id=" . $category_id);
    }
} else {
    $maxSizeMB = FILE_MAX_SIZE / (1024 * 1024); // Convertir a MB
    handle_form_error("Error: El peso máximo es " . $maxSizeMB . " MB", array(
        'name' => $name,
        'description' => $description
    ), "/business/contenido/documentos/add_file.php?category_id=" . $category_id);
}
