<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';

// iniciar sesión y verificar autorización
session_start();

if ($_SESSION['role'] !== 'superadmin') {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

// Recibe los datos del formulario.
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$username = $_POST['username'];
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
    INSERT INTO users (email, password, full_name, username, profile_picture, is_active) 
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("sssssi", $email, $password, $full_name, $username, $profile_picture_path, $is_active);

try {
    // Intenta ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario agregado exitosamente";

        // Cerrar la sentencia y la conexión antes de redirigir
        $stmt->close();
        $mydb->close();

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
