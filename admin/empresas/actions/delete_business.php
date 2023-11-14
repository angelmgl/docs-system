<?php

require '../../../config/config.php';
require '../../../helpers/roles.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['superadmin']);

// Prepara la sentencia para eliminar la empresa
$stmt = $mydb->prepare("DELETE FROM businesses WHERE id = ?");
$stmt->bind_param('s', $_POST['business_id']);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Empresa eliminada satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de empresas
header("Location: " . BASE_URL . "/admin/empresas");
exit;
