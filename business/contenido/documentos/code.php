<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin', 'analyst']);

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
    header("Location: " . BASE_URL . "/business/contenido");
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
    <?php include '../../../components/business/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <div>
                <span class="tag"><?php echo $document["category_name"] ?></span>
                <div class="document-title">
                    <h1><?php echo $document["name"] ?></h1>
                    <?php if($_SESSION["role"] === "admin") { ?>
                    <a href="<?php echo BASE_URL . "/business/contenido/documentos/" . "edit_html.php?document_id=" . $document["id"] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                        </svg>
                    </a>
                    <?php } ?>
                </div>
                <p><?php echo $document["description"] ?></p>
            </div>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . "/business/contenido/categorias/?category_id=" . $document["category_id"] ?>">Regresar</a>
        </div>

        <section style="margin-top: 80px">
            <?php echo $document["code"] ?>
        </section>
    </main>
</body>

</html>