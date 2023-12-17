<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

// Recibir los datos del formulario
$name = $_POST['name'];
$description = $_POST['description'];

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    INSERT INTO categories (name, description, business_id) 
    VALUES (?, ?, ?)
");

$stmt->bind_param("ssi", $name, $description, $my_business);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Categoría agregada exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/business/contenido");
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'name' => $name,
            'description' => $description
        ), "/business/contenido/categorias/add.php");
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $stmt->error, array(
        'name' => $name,
        'description' => $description
    ), "/business/contenido/categorias/add.php");
}

$stmt->close();
$mydb->close();
