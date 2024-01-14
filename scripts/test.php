<?php

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../helpers/mails.php';

$subject = "Test desde " . APP_NAME;
$email = "angelemegeele@gmail.com";
$user_name = "Admin";
$message = "Este es un mensaje de prueba";
$button_url = BASE_URL;
$button_text = "Visitar";
$recommendation = "La prueba fue un éxito";

send_notification($subject, $email, $user_name, $message, $button_url, $button_text, $recommendation);