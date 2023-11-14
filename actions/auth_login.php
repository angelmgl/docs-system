<?php

require '../config/config.php';
require '../helpers/users.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Preparar la consulta para obtener el usuario de la base de datos.
$stmt = $mydb->prepare("SELECT id, full_name, password, is_superuser FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verificar si el usuario existe y, si es así, si la contraseña es correcta.
if ($user && password_verify($password, $user['password'])) {
    // La contraseña es correcta y el usuario existe.
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $user['full_name'];

    // Si es superusuario, se establece el rol y se redirige al dashboard de superadmin.
    if ($user['is_superuser']) {
        $_SESSION['role'] = 'superadmin';
        update_last_login($mydb, $user['id']);
        header("Location: " . BASE_URL . "/admin/dashboard");
        exit;
    } else {
        // Si no es superusuario, redirigimos al Welcome para que seleccione una empresa
        update_last_login($mydb, $user['id']);
        header("Location: " . BASE_URL . "/business/welcome");
        exit;
    }
} else {
    // No se encontró el usuario o la contraseña no es correcta.
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$stmt->close();
$mydb->close();
