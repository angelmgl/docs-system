<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyAuthentication();

$user_id = $_SESSION['user_id'];

// Recibe los datos del formulario.
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$old_photo = $_POST['old_photo'];

$upload_system_dir = "../../uploads/users/";
$upload_url_dir = "/uploads/users/";

// Manejar la subida de la foto de perfil
try {
    if (isset($_FILES['profile_picture'])) {
        $profile_picture_path = upload_photo($_FILES['profile_picture'], $upload_system_dir, $upload_url_dir);
    }
} catch (Exception $e) {
    handle_form_error($e->getMessage(), $_POST, "/perfil");
}

$profile_picture_path = $profile_picture_path ? $profile_picture_path : $old_photo;

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    UPDATE users SET email = ?, full_name = ?, profile_picture = ?
    WHERE id = ?
");

$stmt->bind_param("sssi", $email, $full_name, $profile_picture_path, $user_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success'] = "Perfil actualizado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        header("Location: " . BASE_URL . "/perfil");
        exit;
    } else {
        handle_form_error("Error: " . $stmt->error, array(
            'email' => $email,
            'full_name' => $full_name,
            'username' => $username,
        ), "/perfil");
    }
} catch (Exception $e) {
    handle_form_error("Error: " . $e->getMessage(), array(
        'email' => $email,
        'full_name' => $full_name,
        'username' => $username,
    ), "/perfil");
}

$stmt->close();
$mydb->close();
