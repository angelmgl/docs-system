<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';
require '../../../../helpers/notifications.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibir los datos del formulario
$category_id = $_POST['category_id'];
$business_id = $_POST['business_id'];
$user_id = $_POST['user_id'];

$insert_stmt = $mydb->prepare("INSERT INTO user_categories (user_id, category_id) VALUES (?, ?)");
$insert_stmt->bind_param("ii", $user_id, $category_id);

if (!$insert_stmt->execute()) {
    handle_form_error("Error: " . $stmt->error, array(), "/admin/contenido/categorias/users.php?business_id=$business_id&category_id=$category_id");
}

$insert_stmt->close();

$mydb->close();

// datos para el email
$user_name = $_POST['user_name'];
$user_email = $_POST['user_email'];
$category_name = $_POST['category_name'];
$category_id = $_POST['category_id'];

user_assigned_notification($user_name, $user_email, $category_name, $category_id);

$_SESSION['success'] = "Usuario asignado a la categoría exitosamente";
header("Location: " . BASE_URL . "/admin/contenido/categorias/users.php?business_id=$business_id&category_id=$category_id");
exit;
