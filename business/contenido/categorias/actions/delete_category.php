<?php

require '../../../../config/config.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

// Prepara la sentencia para eliminar la categoría
$stmt = $mydb->prepare("DELETE FROM categories WHERE id = ? AND business_id = ?");
$stmt->bind_param('ii', $_POST['category_id'], $my_business);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Categoría eliminada satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de categorías de la empresa
header("Location: " . BASE_URL . "/business/contenido");
exit;
