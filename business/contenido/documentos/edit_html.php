<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$document_id = isset($_GET["document_id"]) ? (int) $_GET["document_id"] : 0;

$stmt = $mydb->prepare("
    SELECT hd.*, c.business_id
    FROM html_docs hd
    LEFT JOIN categories c ON hd.category_id = c.id
    WHERE hd.id = ?
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
    <main class="container py px">
        <div class="admin-bar">
            <div>
                <h1>Editar documento</h1>
                <p>Fragmento de código HTML, tablas de PowerBI, etc.</p>
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
            }
            ?>

            <form class="custom-form" action="./actions/update_html_doc.php" method="POST">
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

                    <div class="input-wrapper text-input">
                        <label for="code">Fragmento de código: <span class="required">*</span></label>
                        <textarea rows="12" id="code" name="code" required><?php echo $document["code"]; ?> </textarea>
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
    </main>
</body>

</html>