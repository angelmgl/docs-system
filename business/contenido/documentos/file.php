<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/documents.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin', 'analyst']);

$document_id = isset($_GET["document_id"]) ? (int) $_GET["document_id"] : 0;

$stmt = $mydb->prepare("
    SELECT file_docs.*, categories.name AS category_name 
    FROM file_docs 
    LEFT JOIN categories ON file_docs.category_id = categories.id 
    WHERE file_docs.id = ?
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
    <main class="container py px content">
        <div class="admin-bar">
            <div>
                <span class="tag"><?php echo $document["category_name"] ?></span>
                <div class="document-title">
                    <h1><?php echo $document["name"] ?></h1>
                    <?php if($_SESSION["role"] === "admin") { ?>
                    <a href="<?php echo BASE_URL . "/business/contenido/documentos/" . "edit_file.php?document_id=" . $document["id"] ?>">
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
            <div class="file-preview">
                <div class="file-content">
                    <h3><?php echo $document["file_name"] ?></h3>
                    <p><?php echo formatFileSize($document["file_weight"]) ?></p>
                </div>
                <div class="file-actions">
                    <a href="<?php echo BASE_URL . $document['file_path'] ?>" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M432 320H400a16 16 0 0 0 -16 16V448H64V128H208a16 16 0 0 0 16-16V80a16 16 0 0 0 -16-16H48A48 48 0 0 0 0 112V464a48 48 0 0 0 48 48H400a48 48 0 0 0 48-48V336A16 16 0 0 0 432 320zM488 0h-128c-21.4 0-32.1 25.9-17 41l35.7 35.7L135 320.4a24 24 0 0 0 0 34L157.7 377a24 24 0 0 0 34 0L435.3 133.3 471 169c15 15 41 4.5 41-17V24A24 24 0 0 0 488 0z" />
                        </svg>
                    </a>
                    <a href="<?php echo BASE_URL . $document['file_path'] ?>" download="<?php echo $document['file_name'] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512">
                            <path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.5 211.4l-96.4 95.7c-6.7 6.6-17.4 6.6-24 0l-96.4-95.7C73.4 337.3 80.5 320 94.8 320H160v-80c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v80h65.2c14.3 0 21.4 17.3 11.3 27.4zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z" />
                        </svg>
                    </a>
                    <?php if($_SESSION["role"] === "admin") { ?>
                    <a href="<?php echo BASE_URL . "/business/contenido/documentos/" . "edit_file.php?document_id=" . $document["id"] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                        </svg>
                    </a>
                    <?php } ?>
                </div>
            </div>
        </section>
    </main>
</body>

</html>