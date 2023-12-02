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
$code = $_POST['code'];
$category_id = $_POST['category_id'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    INSERT INTO html_docs (name, description, code, category_id) 
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("sssi", $name, $description, $code, $category_id);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Documento agregado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/contenido/categorias/?category_id=" . $category_id);
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
            'description' => $description
        ), "/admin/contenido/categorias/?category_id=" . $category_id);
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $stmt->error, array(
        'name' => $name,
        'description' => $description
    ), "/admin/contenido/categorias/?category_id=" . $category_id);
}

$stmt->close();
$mydb->close();
