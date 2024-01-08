<?php

require '../config/config.php';
require '../helpers/users.php';

session_start();

$code = $_POST['code'];
$password = $_POST['password'];

// Preparar la consulta para obtener el usuario de la base de datos.
$stmt = $mydb->prepare("SELECT * FROM password_resets WHERE recovery_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();

$result = $stmt->get_result();
$reset_code = $result->fetch_assoc();
$stmt->close();

// Verificar si el usuario existe y, si es así, cambiar contraseña
if ($reset_code) {
    // Asegúrate de hashear la contraseña antes de almacenarla.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Actualizar la contraseña del usuario.
    $u_stmt = $mydb->prepare("UPDATE users SET password = ? WHERE id = ?");
    $u_stmt->bind_param("si", $hashed_password, $reset_code["user_id"]);
    $u_stmt->execute();
    $u_stmt->close();

    $mydb->close();

    $_SESSION['success'] = "Contraseña actualizada con éxito, prueba iniciar sesión ahora.";
    header("Location: " . BASE_URL . "/login.php");
    exit;
} else {
    $mydb->close();

    // No se encontró el usuario o la contraseña no es correcta.
    $_SESSION['error'] = "Código de restablecimiento no válido, consigue uno nuevo.";
    header("Location: " . BASE_URL . "/forgot-password.php");
    exit();
}
