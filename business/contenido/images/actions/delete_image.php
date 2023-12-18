<?php

require '../../../../config/config.php';
require '../../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

// Prepara la sentencia para eliminar la imagen
$stmt = $mydb->prepare("DELETE FROM images WHERE id = ?");
$stmt->bind_param('s', $_POST['id']);

// Ejecutar la sentencia
$stmt->execute();

$_SESSION['success'] = "Imagen eliminada satisfactoriamente.";

$stmt->close();
$mydb->close();

// Redirige de vuelta a la lista de imagenes
header("Location: " . BASE_URL . "/business/contenido/documentos/edit_image.php?document_id=" . $_POST['document_id']);
exit;

