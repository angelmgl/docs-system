<?php

require '../../../../config/config.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$table_name = $_POST['doc_type'] . "_docs";

// Prepara la sentencia para eliminar el documento
$stmt = $mydb->prepare("DELETE FROM $table_name WHERE id = ?");
$stmt->bind_param('i', $_POST['document_id']);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Documento eliminado satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de categorías de la empresa
header("Location: " . BASE_URL . "/admin/contenido/categorias/?category_id=" . $_POST['category_id']);
exit;
