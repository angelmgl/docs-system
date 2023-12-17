<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

// Recibe los datos del formulario.
$user_id = $_POST['user_id']; // Asegúrate de enviar el ID del usuario desde el formulario.
$email = $_POST['email'];
$full_name = $_POST['full_name'];
$username = $_POST['username'];
$is_active = isset($_POST['is_active']) ? 1 : 0;
$old_photo = $_POST['old_photo'];
$role = $_POST['role'];
$business_id = $my_business;

$upload_system_dir = "../../../uploads/users/";
$upload_url_dir = "/uploads/users/";

// Manejar la subida de la foto de perfil
try {
    if (isset($_FILES['profile_picture'])) {
        $profile_picture_path = upload_photo($_FILES['profile_picture'], $upload_system_dir, $upload_url_dir);
    }
} catch (Exception $e) {
    handle_form_error($e->getMessage(), $_POST, "/business/usuarios/edit.php?username=" . $username);
}

$profile_picture_path = $profile_picture_path ? $profile_picture_path : $old_photo;

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    UPDATE users SET email = ?, full_name = ?, username = ?, profile_picture = ?, role = ?, is_active = ?
    WHERE id = ? AND business_id = ?
");

$stmt->bind_param("sssssiii", $email, $full_name, $username, $profile_picture_path, $role, $is_active, $user_id, $business_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario actualizado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/business/usuarios");
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(
            'email' => $email,
            'full_name' => $full_name,
            'username' => $username,
        ), "/business/usuarios/edit.php?username=" . $username);
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(
        'email' => $email,
        'full_name' => $full_name,
        'username' => $username,
    ), "/business/usuarios/edit.php?username=" . $username);
}

$stmt->close();
$mydb->close();
