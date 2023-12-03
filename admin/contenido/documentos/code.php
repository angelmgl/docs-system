<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$document_id = isset($_GET["document_id"]) ? (int) $_GET["document_id"] : 0;

$stmt = $mydb->prepare("
    SELECT html_docs.*, categories.name AS category_name 
    FROM html_docs 
    LEFT JOIN categories ON html_docs.category_id = categories.id 
    WHERE html_docs.id = ?
");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();

$document = null;
if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si no se encontró al documento, redirige a la página de lista de documentos.
if ($document === null) {
    header("Location: " . BASE_URL . "/admin/contenido");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Ver documento</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <div>
                <span class="tag"><?php echo $document["category_name"] ?></span>
                <h1><?php echo $document["name"] ?></h1>
                <p><?php echo $document["description"] ?></p>
            </div>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . "/admin/contenido/categorias/?category_id=" . $document["category_id"] ?>">Regresar</a>
        </div>

        <section style="margin-top: 80px">
            <?php echo $document["code"] ?>
        </section>
    </main>
</body>

</html>