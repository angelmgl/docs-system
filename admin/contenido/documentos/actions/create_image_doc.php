<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();
verifyRoles(['super']);

// Recibir los datos del formulario
$name = $_POST['name'];
$description = $_POST['description'];
$category_id = $_POST['category_id'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    INSERT INTO image_docs (name, description, category_id) 
    VALUES (?, ?, ?)
");

$stmt->bind_param("ssi", $name, $description, $category_id);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $document_id = $mydb->insert_id;
        $_SESSION['success'] = "Documento creado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/contenido/documentos/edit_image.php?document_id=" . $document_id);
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
            'description' => $description
        ), "/admin/contenido/documentos/add_html.php?category_id=" . $category_id);
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $stmt->error, array(
        'name' => $name,
        'description' => $description
    ), "/admin/contenido/documentos/add_html.php?category_id=" . $category_id);
}

$stmt->close();
$mydb->close();
