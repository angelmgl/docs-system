<?php

require '../../../config/config.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$document_id = $_GET["document_id"];
$doc_type = $_GET["doc_type"];

$document = null;

$table_name = $doc_type . "_docs";

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM $table_name WHERE id = ?");
$stmt->bind_param("i", $document_id);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si no se encontró al usuario, redirige a la página de lista de usuarios.
if ($document === null) {
    header("Location: " . BASE_URL . "/business/contenido/categorias/?category_id=" . $document["category_id"]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Eliminar documento</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/business/header.php'; ?>
    <main class="container px py content" id="remove-user">
        <h1>¿Estás seguro de que quieres eliminar <?php echo $document["name"] ?>?</h1>

        <p>
            ¡Atención! Eliminar un documento es una acción irreversible. Si no estás completamente
            seguro cancela esta acción.
        </p>

        <div class="remove-actions">
            <form action="./actions/delete_document.php" method="POST">
                <input type="hidden" name="document_id" value="<?php echo $document_id; ?>">
                <input type="hidden" name="category_id" value="<?php echo $document["category_id"]; ?>">
                <input type="hidden" name="doc_type" value="<?php echo $doc_type; ?>">
                <button type="submit" class="btn btn-primary">Si, eliminar documento</button>
            </form>
            <a href="<?php echo BASE_URL . "/business/contenido/categorias/?category_id=" . $document["category_id"] ?>" class="btn btn-secondary">No, cancelar</a>
        </div>
    </main>
</body>

</html>