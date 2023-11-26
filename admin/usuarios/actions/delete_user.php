<?php

require '../../../config/config.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Prepara la sentencia para eliminar el usuario
$stmt = $mydb->prepare("DELETE FROM users WHERE username = ?");
$stmt->bind_param('s', $_POST['username']);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Usuario eliminado satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de usuarios
header("Location: " . BASE_URL . "/admin/usuarios");
exit;
