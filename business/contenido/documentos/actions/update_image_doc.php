<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

// Recibe los datos del formulario.
$document_id = $_POST['document_id']; // Asegúrate de enviar el ID del documento desde el formulario.
$name = $_POST['name'];
$description = $_POST['description'];
$category_id = $_POST['category_id'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    UPDATE image_docs SET name = ?, description = ?, category_id = ?
    WHERE id = ?
");

$stmt->bind_param("ssii", $name, $description, $category_id, $document_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Documento actualizado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/business/contenido/categorias/?category_id=" . $category_id);
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
        ), "/business/contenido/documentos/edit_html.php?document_id=" . $document_id);
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(
        'name' => $name,
    ), "/business/contenido/documentos/edit_html.php?document_id=" . $document_id);
}

$stmt->close();
$mydb->close();
