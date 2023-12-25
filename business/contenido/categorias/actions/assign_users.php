<?php

require '../../../../config/config.php';
require '../../../../helpers/forms.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

// Recibir los datos del formulario
$category_id = $_POST['category_id'];
$business_id = $_SESSION['business_id'];
$marked_users = [];

foreach ($_POST as $key => $value) {
    if (strpos($key, 'user_') === 0) {
        $user_id = substr($key, 5);
        $marked_users[] = $user_id;
    }
}
// Eliminar todas las relaciones existentes para esta categoría
$delete_stmt = $mydb->prepare("DELETE FROM user_categories WHERE category_id = ?");
$delete_stmt->bind_param("i", $category_id);
$delete_stmt->execute();
$delete_stmt->close();

// Insertar nuevas relaciones basadas en los usuarios marcados
foreach ($marked_users as $user_id) {
    $insert_stmt = $mydb->prepare("INSERT INTO user_categories (user_id, category_id) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $user_id, $category_id);

    if (!$insert_stmt->execute()) {
        handle_form_error("Error: " . $stmt->error, array(), "/business/contenido/categorias/users.php?category_id=$category_id");
    }
    $insert_stmt->close();
}

$mydb->close();

// Redirigir o manejar el post-proceso
if(empty($marked_users)) {
    $_SESSION['success'] = "Usuarios eliminados exitosamente";
} else {
    $_SESSION['success'] = "Permisos modificados exitosamente";
}
header("Location: " . BASE_URL . "/business/contenido/categorias/users.php?category_id=$category_id");
exit;
