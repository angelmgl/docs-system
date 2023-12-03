<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$document_id = isset($_GET["document_id"]) ? (int) $_GET["document_id"] : 0;

$stmt = $mydb->prepare("
    SELECT id.id, id.name, id.description, id.category_id, c.business_id, i.id AS image_id, i.image_path
    FROM image_docs id
    LEFT JOIN categories c ON id.category_id = c.id
    LEFT JOIN images i ON id.id = i.document_id
    WHERE id.id = ?
");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();

$document = [
    'id' => '',
    'name' => '',
    'description' => '',
    'category_id' => '',
    'business_id' => '',
    'images' => []
];

if ($result->num_rows > 0) {
    $isFirstRow = true;
    while ($row = $result->fetch_assoc()) {
        if ($isFirstRow) {
            // Asignar datos del documento en la primera iteración
            $document['id'] = $row['id'];
            $document['name'] = $row['name'];
            $document['description'] = $row['description'];
            $document['category_id'] = $row['category_id'];
            $document['business_id'] = $row['business_id'];
            $isFirstRow = false;
        }

        // Agregar imágenes si existen
        if (isset($row['image_path']) && $row['image_path'] !== null) {
            $document['images'][] = [
                'image_path' => $row['image_path'],
                'image_id' => $row['image_id']
            ];
        }
    }
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
    header("Location: " . BASE_URL . "/admin/contenido");
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
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <div>
                <h1>Editar documento</h1>
                <p>Este documento es una colección de imágenes.</p>
            </div>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . "/admin/contenido/categorias/?category_id=" . $document["category_id"] ?>">Regresar</a>
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

            <form class="custom-form" action="./actions/update_image_doc.php" method="POST">
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
            <h2>Administrar imágenes</h2>
            <form class="admin-form" action="./actions/upload_images.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="document_id" value="<?php echo $document_id ?>">
                <div class="images-input">
                    <label>Selecciona las imágenes...</label>
                    <input type="file" id="images" class="show" name="images[]" accept=".jpg, .jpeg, .png, .webp" multiple>
                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Subir imágenes">
                </div>
            </form>

            <?php if (!empty($document['images'])) { ?>
                <section class="images-grid">
                    <?php
                    foreach ($document['images'] as $image) {
                        include "../../../components/admin/image_container.php";
                    }
                    ?>
                </section>
            <?php } else { ?>
                <div>
                    Esta propiedad no posee imágenes...
                </div>
            <?php } ?>
        </section>
    </main>
</body>

</html>