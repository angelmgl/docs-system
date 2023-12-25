<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibir los datos del formulario
$category_id = $_POST['category_id'];
$business_id = $_POST['business_id'];
$user_id = $_POST['user_id'];

$insert_stmt = $mydb->prepare("DELETE FROM user_categories WHERE user_id = ? AND category_id = ?");
$insert_stmt->bind_param("ii", $user_id, $category_id);

if (!$insert_stmt->execute()) {
    handle_form_error("Error: " . $stmt->error, array(), "/admin/contenido/categorias/users.php?business_id=$business_id&category_id=$category_id");
}

$insert_stmt->close();

$mydb->close();

$_SESSION['success'] = "Usuario eliminado de la categoría exitosamente";
header("Location: " . BASE_URL . "/admin/contenido/categorias/users.php?business_id=$business_id&category_id=$category_id");
exit;
