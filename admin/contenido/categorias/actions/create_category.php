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
$business_id = $_POST['business_id'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    INSERT INTO categories (name, description, business_id) 
    VALUES (?, ?, ?)
");

$stmt->bind_param("ssi", $name, $description, $business_id);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Categoría agregada exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/admin/contenido?business_id=$business_id");
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
            'description' => $description
        ), "/admin/contenido/categorias/add.php?business_id=$business_id");
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $stmt->error, array(
        'name' => $name,
        'description' => $description
    ), "/admin/contenido/categorias/add.php?business_id=$business_id");
}

$stmt->close();
$mydb->close();
