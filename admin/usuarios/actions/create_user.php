<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/mails.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibe los datos del formulario.
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$username = $_POST['username'];
$role = $_POST['role'];
$business_id = $_POST['business_id'];
$is_active = isset($_POST['is_active']) ? 1 : 0;


// Iniciar la variable $profile_picture_path con NULL
$profile_picture_path = NULL;

$upload_system_dir = "../../../uploads/users/"; // Asegúrate de tener este directorio creado y con permisos de escritura
$upload_url_dir = "/uploads/users/";

// Manejar la subida de la foto de perfil
try {
    if (isset($_FILES['profile_picture'])) {
        $profile_picture_path = upload_photo($_FILES['profile_picture'], $upload_system_dir, $upload_url_dir);
    }
} catch (Exception $e) {
    handle_form_error($e->getMessage(), $_POST, "/admin/usuarios/add.php");
}

// Conexión a la base de datos y preparación de la consulta.
$stmt = $mydb->prepare("
    INSERT INTO users (email, password, full_name, username, profile_picture, role, business_id, is_active) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("ssssssii", $email, $password, $full_name, $username, $profile_picture_path, $role, $business_id, $is_active);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario agregado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

        $pw = $_POST['password'];
        $subject = "Has sido registrado en " . APP_NAME; 
        $user_email = $email;
        $user_name = $full_name;
        $message = "Ahora puedes iniciar sesión con los siguientes detalles:<br><br>Nombre de usuario: $username<br>Contraseña: $pw"; 
        $button_url = BASE_URL . "/login.php";
        $button_text =  "Iniciar sesión";
        $recommendation = "Ingresa a tu cuenta, completa tu perfil, actualiza tu contraseña y comienza a trabajar.";
        send_notification($subject, $user_email, $user_name, $message, $button_url, $button_text, $recommendation);

        header("Location: " . BASE_URL . "/admin/usuarios");
        exit;
    } else {
        // Si hay un error, lo manejamos
        handle_form_error("Error: " . $stmt->error, array(
            'email' => $email,
            'full_name' => $full_name,
            'username' => $username,
        ), "/admin/usuarios/add.php");
    }
} catch (Exception $e) {
    // Esto atrapará cualquier excepción o error fatal que ocurra
    handle_form_error("Error: " . $e->getMessage(), array(
        'email' => $email,
        'full_name' => $full_name,
        'username' => $username,
    ), "/admin/usuarios/add.php");
}

$stmt->close();
$mydb->close();
