<?php

require '../../../config/config.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$image = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM images WHERE id = ?");
$stmt->bind_param("i", $id);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $image = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si no se encontró la imagen, redirige a la página de propiedades
if ($image === null) {
    header("Location: " . BASE_URL . "/admin/contenido");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Eliminar imagen</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container px py" id="remove-image">
        <img src="<?php echo BASE_URL . $image["image_path"] ?>" class="image-to-remove" />

        <h1>¿Estás seguro de que quieres eliminar esta imagen?</h1>

        <div class="remove-actions">
            <form action="./actions/delete_image.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $image["id"]; ?>">
                <input type="hidden" name="document_id" value="<?php echo $image["document_id"]; ?>">
                <button type="submit" class="btn btn-primary">Si, eliminar</button>
            </form>
            <a href="<?php echo BASE_URL ?>/admin/contenido/documentos/edit_image.php?document_id=<?php echo $image["document_id"] ?>" class="btn btn-secondary">No, cancelar</a>
        </div>
    </main>
</body>

</html>