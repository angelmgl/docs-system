<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/business.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$category_id = $_GET["category_id"];

$category = null;

// preparar la consulta
$stmt = $mydb->prepare("
    SELECT c.*, b.name AS business_name, b.logo 
    FROM categories c
    LEFT JOIN businesses b ON c.business_id = b.id
    WHERE c.id = ?
");
$stmt->bind_param("i", $category_id);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $category = $result->fetch_assoc();
}

$stmt->close();

// Si no se encontró a la categoría, redirige a la página de lista de categorías.
if ($category === null) {
    header("Location: " . BASE_URL . "/admin/contenido");
    exit;
}

$docs_stmt = $mydb->prepare("
    SELECT 'html' as type, id, name, description FROM html_docs WHERE category_id = ?
    UNION
    SELECT 'file' as type, id, name, description FROM file_docs WHERE category_id = ?
    UNION
    SELECT 'image' as type, id, name, description FROM image_docs WHERE category_id = ?
");
$docs_stmt->bind_param("iii", $category_id, $category_id, $category_id);
$docs_stmt->execute();

$result = $docs_stmt->get_result();
$docs = [];
while ($row = $result->fetch_assoc()) {
    $docs[] = $row;
}

$docs_stmt->close();

$mydb->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title><?php echo $category['name']; ?></title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1><?php echo $category['name']; ?></h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/contenido/?business_id=<?php echo $category["business_id"] ?>">Regresar</a>
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

            <div class="my-business-card">
                <div class="logo" style="background-image: url(<?php echo get_logo($category) ?>)"></div>
                <div class="my-business-content">
                    <p class="my-business-name"><?php echo $category["business_name"] ?></p>
                </div>
            </div>

            <div class="category-actions">
                <button class="btn btn-primary" id="create-doc">Agregar documento</button>
                <div class="profile-nav">
                    <a class="profile-link" href="<?php echo BASE_URL . '/admin/contenido/documentos/add_image.php?category_id=' . $category_id ?>">
                        Imágenes
                    </a>
                    <a class="profile-link" href="<?php echo BASE_URL . '/admin/contenido/documentos/add.php?doc_type=file&category_id=' . $category_id ?>">
                        Documentos
                    </a>
                    <a class="profile-link" href="<?php echo BASE_URL . '/admin/contenido/documentos/add_html.php?category_id=' . $category_id ?>">
                        Fragmento de código
                    </a>
                </div>
            </div>

            <?php if (empty($docs)) { ?>
                <p>No hay documentos de esta categoría...</p>
            <?php } else {
                include '../../../components/admin/docs_table.php';
            } ?>
        </section>
    </main>

    <script src="<?php echo BASE_URL ?>/assets/js/category.js"></script>
</body>

</html>