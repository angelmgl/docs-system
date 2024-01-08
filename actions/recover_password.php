<?php

require '../config/config.php';
require '../helpers/users.php';
require '../helpers/mails.php';
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

    $subject = "Recupera tu contraseña en " . APP_NAME;
    $user_email = $user["email"];
    $user_name = $user["full_name"];
    $message = "Para cambiar tu contraseña, ingresa al enlace a continuación y verás un formulario para establecer una nueva. Recuerda guardarla en un lugar seguro.";
    $button_url = BASE_URL . "/change-password.php?code=" . $reset_code;
    $button_text = "Recuperar contraseña";
    $recommendation = "<span style=\"font-size: 12px\">Sino puedes ingresar con el botón anterior, copia y pega el siguiente enlace en tu navegador: $button_url</span>";
    send_notification($subject, $user_email, $user_name, $message, $button_url, $button_text, $recommendation);

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
