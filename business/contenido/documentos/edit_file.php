<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/documents.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$document_id = isset($_GET["document_id"]) ? (int) $_GET["document_id"] : 0;

$stmt = $mydb->prepare("
    SELECT fd.*, c.business_id
    FROM file_docs fd
    LEFT JOIN categories c ON fd.category_id = c.id
    WHERE fd.id = ?
");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();

$document = null;
if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
}

$stmt->close();

// consulta por todas las demás categorías de este negocio
$business_id = $document["business_id"];

$b_stmt = $mydb->prepare("SELECT * FROM categories WHERE business_id = $business_id");
$b_stmt->execute();
$result = $b_stmt->get_result();

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$b_stmt->close();
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
    <title>Editar documento</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/business/header.php'; ?>
    <main class="container py px content">
        <div class="admin-bar">
            <div>
                <h1>Editar documento</h1>
                <p>Documento PDF, Excel, Word, CSV, etc.</p>
            </div>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . "/business/contenido/categorias/?category_id=" . $document["category_id"] ?>">Regresar</a>
        </div>

        <section>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">';
                echo $_SESSION['error'];
                echo '</p>';
                unset($_SESSION['error']);
            } else if (isset($_SESSION['success'])) {
                echo '<p class="success">';
                echo $_SESSION['success'];
                echo '</p>';
                unset($_SESSION['success']);
            }
            ?>

            <form class="custom-form" action="./actions/update_file_doc.php" method="POST">
                <input type="hidden" name="document_id" value="<?php echo $document_id ?>">

                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="name">Título: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo $document["name"] ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="description">Descripción:</label>
                        <textarea rows="4" id="description" name="description"><?php echo $document["description"]; ?> </textarea>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper select-input">
                        <label for="category_id">Seleccionar categoría:</label>
                        <select id="category_id" name="category_id">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category["id"] ?>" <?php echo $document['category_id'] === $category['id'] ? 'selected' : '' ?>>
                                    <?php echo $category["name"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar Documento">
                </div>
            </form>

            <?php unset($_SESSION['form_data']); ?>
        </section>

        <hr style="margin: 40px 0;" />

        <!-- inicia sección de imágenes -->
        <section>
            <h2>Administrar documento</h2>

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
                    <a href="#" id="edit">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                        </svg>
                    </a>
                </div>
            </div>

            <form style="margin-top: 20px;" class="admin-form hidden" action="./actions/upload_file.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="document_id" value="<?php echo $document_id ?>">
                <div class="images-input">
                    <label>Nuevo documento...</label>
                    <input type="file" id="images" class="show" name="file" accept=".ps, .pdf, .xps, .odf, .docx, .rtf, .odt, .txt, .html, .wps, .csv, .xlsx, .xls, .ods, .pptx, .ppt, .odp" required>
                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar documento">
                    <button style="margin-left: 10px" id="cancel" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </section>
    </main>

    <script>
        const form = document.querySelector(".admin-form");
        const preview = document.querySelector(".file-preview");
        const btn = document.getElementById("edit");
        const cancel = document.getElementById("cancel");

        btn.addEventListener("click", () => {
            form.classList.remove("hidden");
            preview.classList.add("hidden");
        })

        cancel.addEventListener("click", () => {
            form.classList.add("hidden");
            preview.classList.remove("hidden");
        })
    </script>
</body>

</html>