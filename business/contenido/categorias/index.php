<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/business.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

$category_id = $_GET["category_id"];
$doc_type_value = isset($_GET['doc_type']) ? htmlspecialchars($_GET['doc_type']) : '';

$category = null;

// preparar la consulta
$stmt = $mydb->prepare("
    SELECT c.*, b.name AS business_name, b.logo 
    FROM categories c
    LEFT JOIN businesses b ON c.business_id = b.id
    WHERE c.id = ? AND c.business_id = ?
");
$stmt->bind_param("ii", $category_id, $my_business);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $category = $result->fetch_assoc();
}

$stmt->close();

// Si no se encontró a la categoría, redirige a la página de lista de categorías.
if ($category === null) {
    header("Location: " . BASE_URL . "/business/contenido");
    exit;
}

$doc_type_value = isset($_GET['doc_type']) ? htmlspecialchars($_GET['doc_type']) : '';

$sql = "";
$params = [];
$types = "";

// Validar y construir la consulta según el tipo de documento
$valid_doc_types = ['html', 'file', 'image'];
if (in_array($doc_type_value, $valid_doc_types)) {
    $sql = "SELECT '$doc_type_value' AS type, id, name, description FROM {$doc_type_value}_docs WHERE category_id = ?";
    $types = 'i';
    $params[] = &$category_id;
} else {
    // Si no se selecciona ningún tipo, incluir todas las tablas
    $sql = "
        SELECT 'html' as type, id, name, description FROM html_docs WHERE category_id = ?
        UNION
        SELECT 'file' as type, id, name, description FROM file_docs WHERE category_id = ?
        UNION
        SELECT 'image' as type, id, name, description FROM image_docs WHERE category_id = ?
    ";
    $types = 'iii';
    $params[] = &$category_id;
    $params[] = &$category_id;
    $params[] = &$category_id;
}

$docs_stmt = $mydb->prepare($sql);
call_user_func_array([$docs_stmt, 'bind_param'], array_merge([$types], $params));
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
    <?php include '../../../components/business/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1><?php echo $category['name']; ?></h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/business/contenido">Regresar</a>
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
                <!-- filtrar por tipo de documento -->
                <form method="GET" class="custom-form grid cols-2" style="max-width: 50%; margin-top: 0">
                    <input type="hidden" name="category_id" value="<?php echo $category_id ?>" />
                    <div class="input-wrapper select-input">
                        <label for="doc_type">Seleccionar tipo de documento:</label>
                        <select id="doc_type" name="doc_type">
                            <option value="">Selecciona...</option>
                            <option value="html" <?php echo $doc_type_value === 'html' ? 'selected' : ''; ?>>HTML</option>
                            <option value="file" <?php echo $doc_type_value === 'file' ? 'selected' : ''; ?>>Documento</option>
                            <option value="image" <?php echo $doc_type_value === 'image' ? 'selected' : ''; ?>>Imágenes</option>
                        </select>
                    </div>
                    <!-- buscar -->
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <button class="btn btn-primary" id="create-doc">Agregar documento</button>

                <div class="profile-nav">
                    <a class="profile-link" href="<?php echo BASE_URL . '/business/contenido/documentos/add_image.php?category_id=' . $category_id ?>">
                        Imágenes
                    </a>
                    <a class="profile-link" href="<?php echo BASE_URL . '/business/contenido/documentos/add_file.php?category_id=' . $category_id ?>">
                        Documentos
                    </a>
                    <a class="profile-link" href="<?php echo BASE_URL . '/business/contenido/documentos/add_html.php?category_id=' . $category_id ?>">
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