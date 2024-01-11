<?php

require __DIR__ . '/mails.php';

function recover_password_notification($email, $full_name, $reset_code) {
    $subject = "Recupera tu contraseña en " . APP_NAME;
    $user_email = $email;
    $user_name = $full_name;
    $message = "Para cambiar tu contraseña, ingresa al enlace a continuación y verás un formulario para establecer una nueva. Recuerda guardarla en un lugar seguro.";
    $button_url = BASE_URL . "/change-password.php?code=" . $reset_code;
    $button_text = "Recuperar contraseña";
    $recommendation = "<span style=\"font-size: 12px\">Sino puedes ingresar con el botón anterior, copia y pega el siguiente enlace en tu navegador: $button_url</span>";
    send_notification($subject, $user_email, $user_name, $message, $button_url, $button_text, $recommendation);
}

function user_created_notification($password, $email, $full_name, $username) {
    $subject = "Has sido registrado en " . APP_NAME; 
    $user_email = $email;
    $user_name = $full_name;
    $message = "Ahora puedes iniciar sesión con los siguientes detalles:<br><br>Nombre de usuario: $username<br>Contraseña: $password"; 
    $button_url = BASE_URL . "/login.php";
    $button_text =  "Iniciar sesión";
    $recommendation = "Ingresa a tu cuenta, completa tu perfil, actualiza tu contraseña y comienza a trabajar.";
    send_notification($subject, $user_email, $user_name, $message, $button_url, $button_text, $recommendation);
}

function user_assigned_notification($full_name, $email, $category_name, $category_id) {
    $subject = "Te han asignado la categoría " . $category_name; 
    $user_email = $email;
    $user_name = $full_name;
    $message = "Los administradores de tu negocio te han asignado una categoría nueva: " . $category_name; 
    $button_url = BASE_URL . "/business/contenido/categorias/?category_id=" . $category_id;
    $button_text =  "Ver categoría";
    $recommendation = "Ingresa a tu cuenta para comenzar a trabajar con tus nuevos documentos.";
    send_notification($subject, $user_email, $user_name, $message, $button_url, $button_text, $recommendation);
}