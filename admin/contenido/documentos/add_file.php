<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$category_id = isset($_GET["category_id"]) ? (int) $_GET["category_id"] : 0;

$stmt = $mydb->prepare("SELECT * FROM categories WHERE id = $category_id");
$stmt->execute();
$result = $stmt->get_result();

$category = null;
if ($result->num_rows > 0) {
    $category = $result->fetch_assoc();
}

$stmt->close();

// consulta por todas las demás categorías de este negocio
$business_id = $category["business_id"];

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

// Si no se encontró a la categoría, redirige a la página de lista de categorías.
if ($category === null) {
    header("Location: " . BASE_URL . "/admin/contenido");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar documento</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <div>
                <h1>Agregar documento</h1>
                <p>Documento PDF, Excel, Word, CSV, etc.</p>
            </div>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . "/admin/contenido/categorias/?category_id=" . $category_id ?>">Regresar</a>
        </div>

        <section>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">';
                echo $_SESSION['error'];
                echo '</p>';
                unset($_SESSION['error']);
            }
            ?>

            <form class="custom-form" action="./actions/create_file_doc.php" method="POST" enctype="multipart/form-data">
                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="name">Título: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo get_form_data('name'); ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="description">Descripción:</label>
                        <textarea rows="4" id="description" name="description"><?php echo get_form_data('description'); ?> </textarea>
                    </div>

                    <div class="input-wrapper file-input">
                        <label for="file">Documento:</label>
                        <input type="file" id="file" name="file" class="show" required
                        accept=".ps, .pdf, .xps, .odf, .docx, .rtf, .odt, .txt, .html, .wps, .csv, .xlsx, .xls, .ods, .pptx, .ppt, .odp">
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper select-input">
                        <label for="category_id">Seleccionar categoría:</label>
                        <select id="category_id" name="category_id">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category["id"] ?>" <?php echo $category_id === $category['id'] ? 'selected' : '' ?>>
                                    <?php echo $category["name"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Crear Documento">
                </div>
            </form>

            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>