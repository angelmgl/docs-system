<?php

require '../config/config.php';
require '../helpers/users.php';
require '../helpers/notifications.php';

session_start();

$email = $_POST['email'];

// Preparar la consulta para obtener el usuario de la base de datos.
$stmt = $mydb->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Verificar si el usuario existe y, si es así, recuperar contraseña
if ($user) {
    $reset_code = generate_reset_code();
    
    $r_stmt = $mydb->prepare("INSERT INTO password_resets (user_id, recovery_code) VALUES (?, ?)");
    $r_stmt->bind_param("is", $user["id"], $reset_code);
    $r_stmt->execute();
    $r_stmt->close();

    recover_password_notification($user["email"], $user["full_name"], $reset_code);

    $mydb->close();

    header("Location: " . BASE_URL . "/code-sent.php?email=" . $user['email']);
    exit();
} else {
    $mydb->close();

    // No se encontró el usuario o la contraseña no es correcta.
    $_SESSION['error'] = "No se ha encontrado un usuario con el correo electrónico proporcionado.";
    header("Location: " . BASE_URL . "/forgot-password.php");
    exit();
}
