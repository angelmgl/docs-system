<?php

require '../../../../config/config.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Prepara la sentencia para eliminar la categoría
$stmt = $mydb->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param('i', $_POST['category_id']);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Categoría eliminada satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de categorías de la empresa
header("Location: " . BASE_URL . "/admin/contenido/?business_id=" . $_POST['business_id']);
exit;
