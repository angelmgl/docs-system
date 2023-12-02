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

// consulta de documentos
// $business_stmt = $mydb->prepare("SELECT * FROM businesses WHERE is_active = 1");
// $business_stmt->execute();
// $business_result = $business_stmt->get_result();

// $businesses = [];
// if ($business_result->num_rows > 0) {
//     while ($row = $business_result->fetch_assoc()) {
//         $businesses[] = $row;
//     }
// }

// $business_stmt->close();

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
                    <a class="profile-link" href="#">Imágenes</a>
                    <a class="profile-link" href="#">Documentos</a>
                    <a class="profile-link" href="#">Fragmento de código</a>
                </div>
            </div>

            <p>lista de documentos aquí...</p>
        </section>
    </main>
    
    <script src="<?php echo BASE_URL ?>/assets/js/category.js"></script>
</body>

</html>