<?php

require '../config/config.php';
require '../helpers/users.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Preparar la consulta para obtener el usuario de la base de datos.
$stmt = $mydb->prepare("SELECT * FROM users WHERE username = ?");
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
    $_SESSION['business_id'] = $user['business_id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['profile_picture'] = $user['profile_picture'];

    update_last_login($mydb, $user['id']);

    $stmt->close();
    $mydb->close();

    // Si es superadmin, se establece el rol y se redirige al dashboard de superadmin.
    if ($user['role'] === 'super') {
        header("Location: " . BASE_URL . "/admin/dashboard");
    } else if (!!$user['business_id']) {
        // Si no es superadmin y tiene un negocio, redirigimos al Welcome para que seleccione una empresa
        header("Location: " . BASE_URL . "/business/dashboard");
    } else {
        // Si el negocio no está asignado
        $_SESSION['error'] = "El usuario no tiene un negocio asignado.";
        header("Location: " . BASE_URL . "/login.php");
    }
    exit();
} else {
    $stmt->close();
    $mydb->close();

    // No se encontró el usuario o la contraseña no es correcta.
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: " . BASE_URL . "/login.php");
    exit();
}