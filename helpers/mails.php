<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/Exception.php';
require __DIR__ . '/../PHPMailer/PHPMailer.php';
require __DIR__ . '/../PHPMailer/SMTP.php';

function send_notification($subject, $user_email, $user_name, $message, $button_url, $button_text, $recommendation)
{
    $template = file_get_contents(__DIR__ . '/email_template.html');

    // Reemplaza los placeholders con contenido dinámico
    $template = str_replace('{subject}', $subject, $template);
    $template = str_replace('{name}', $user_name, $template);
    $template = str_replace('{message}', $message, $template);
    $template = str_replace('{button_url}', $button_url, $template);
    $template = str_replace('{button_text}', $button_text, $template);
    $template = str_replace('{recommendation}', $recommendation, $template);

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        // Configuración del servidor
        $mail->SMTPDebug = 0;  // Habilita el modo de depuración (0 para desactivarlo)
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   =  MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipientes
        $mail->setFrom(MAIL_FROM, APP_NAME);
        $mail->addAddress($user_email, $user_name);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $template;

        $mail->send();
        echo 'El mensaje ha sido enviado';
    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
    }
}
