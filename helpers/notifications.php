<?php

require '../helpers/mails.php';

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

