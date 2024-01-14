<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$document_id = isset($_GET["document_id"]) ? (int) $_GET["document_id"] : 0;

$stmt = $mydb->prepare("
    SELECT id.id, id.name, id.description, id.category_id, c.name AS category_name, i.width, i.height, i.image_path
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
    'category_name' => '',
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
            $document['category_name'] = $row['category_name'];
            $isFirstRow = false;
        }

        // Agregar imágenes si existen
        if (isset($row['image_path']) && $row['image_path'] !== null) {
            $document['images'][] = [
                'image_path' => $row['image_path'],
                'width' => $row['width'],
                'height' => $row['height']
            ];
        }
    }
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
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/assets/css/photoswipe.css" />
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px content">
        <div class="admin-bar">
            <div>
                <span class="tag"><?php echo $document["category_name"] ?></span>
                <div class="document-title">
                    <h1><?php echo $document["name"] ?></h1>
                    <a href="<?php echo BASE_URL . "/admin/contenido/documentos/" . "edit_image.php?document_id=" . $document["id"] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                        </svg>
                    </a>
                </div>
                <p><?php echo $document["description"] ?></p>
            </div>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . "/admin/contenido/categorias/?category_id=" . $document["category_id"] ?>">Regresar</a>
        </div>

        <section class="photo-grid" id="gallery">
            <?php if (isset($document['images']) && !empty($document['images'])) : ?>
                <?php foreach ($document['images'] as $image) : ?>
                    <div class="photo-container">
                        <a class="op" data-pswp-src="<?php echo BASE_URL . $image['image_path']; ?>" data-pswp-width=<?php echo $image['width']; ?> data-pswp-height=<?php echo $image['height']; ?>>
                            <img class="document-photo" src="<?php echo BASE_URL . $image['image_path']; ?>" alt="Imagen del documento">
                        </a>
                    </div>
                <?php endforeach; ?>
                <a class="add-image" href="<?php echo BASE_URL . "/admin/contenido/documentos/" . "edit_image.php?document_id=" . $document["id"] ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                        <path fill="currentColor" d="M416 208H272V64c0-17.7-14.3-32-32-32h-32c-17.7 0-32 14.3-32 32v144H32c-17.7 0-32 14.3-32 32v32c0 17.7 14.3 32 32 32h144v144c0 17.7 14.3 32 32 32h32c17.7 0 32-14.3 32-32V304h144c17.7 0 32-14.3 32-32v-32c0-17.7-14.3-32-32-32z" />
                    </svg>
                </a>
            <?php else : ?>
                <p>Este documento no tiene imágenes...</p>
            <?php endif; ?>
        </section>
    </main>

    <!-- Initialize Swiper and PhotoSwipe -->
    <script type="module">
        import PhotoSwipeLightbox from '<?php echo BASE_URL ?>/assets/js/photoswipe-lightbox.esm.js';
        import PhotoSwipe from '<?php echo BASE_URL ?>/assets/js/photoswipe.esm.js';

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#gallery',
            children: '.op',
            pswpModule: PhotoSwipe
        });

        lightbox.init();
    </script>
</body>

</html>