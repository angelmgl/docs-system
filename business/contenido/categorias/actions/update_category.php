<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);
$my_business = $_SESSION['business_id'];

// Recibir los datos del formulario
$category_id = $_POST['category_id'];
$name = $_POST['name'];
$description = $_POST['description'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    UPDATE categories SET name = ?, description = ?
    WHERE id = ? AND business_id = ?
");

$stmt->bind_param("ssii", $name, $description, $category_id, $my_business);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Categoría actualizada exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/business/contenido?business_id=$business_id");
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
            'description' => $description
        ), "/business/contenido/categorias/edit.php?category_id=$category_id");
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $stmt->error, array(
        'name' => $name,
        'description' => $description
    ), "/business/contenido/categorias/edit.php?category_id=$category_id");
}

$stmt->close();
$mydb->close();
