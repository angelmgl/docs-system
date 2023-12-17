<?php

require '../../../config/config.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);
$my_business = $_SESSION['business_id'];

// Prepara la sentencia para eliminar el usuario
$stmt = $mydb->prepare("DELETE FROM users WHERE username = ? AND business_id = ?");
$stmt->bind_param('si', $_POST['username'], $my_business);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Usuario eliminado satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de usuarios
header("Location: " . BASE_URL . "/business/usuarios");
exit;
