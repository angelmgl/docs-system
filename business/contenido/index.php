<?php

require '../../config/config.php';
require '../../helpers/auth.php';
require '../../helpers/business.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin', 'analyst']);

$my_business = $_SESSION['business_id'];
$my_id = $_SESSION['user_id'];

$categories = [];

if ($my_business && $_SESSION["role"] === 'admin') {
    $stmt = $mydb->prepare("SELECT * FROM categories WHERE business_id = ?");
    $stmt->bind_param("i", $my_business);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    $stmt->close();
} elseif ($my_business && $_SESSION["role"] === 'analyst') {
    $stmt = $mydb->prepare("
        SELECT c.* 
        FROM categories c
        JOIN user_categories uc ON c.id = uc.category_id
        WHERE c.business_id = ? AND uc.user_id = ?
    ");
    $stmt->bind_param("ii", $my_business, $my_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    $stmt->close();
}

$mydb->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Contenido</title>
    <?php include '../../components/meta.php' ?>
</head>

<body>
    <?php include '../../components/business/header.php' ?>
    <section class="container py px">
        <div class="admin-bar">
            <h1>Administrar Contenido</h1>
        </div>

        <?php
        if (isset($_SESSION['success'])) {
            echo '<p class="success">';
            echo $_SESSION['success'];
            echo '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <div>
            <section style="margin-top: 40px">
                <?php if (empty($categories)) { ?>
                    <p>Esta empresa no tiene categorías creadas aún...</p>
                    <?php } else {
                    foreach ($categories as $category) { ?>
                        <div class="category-item">
                            <div class="category-header">
                                <h2><?php echo $category["name"] ?></h2>
                                <p><?php echo $category["description"] ?></p>
                                <?php if ($_SESSION["role"] === "admin") { ?>
                                    <div class="actions">
                                        <a href="<?php echo BASE_URL ?>/business/contenido/categorias/edit.php?category_id=<?php echo $category["id"]; ?>" class="action edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                                <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                            </svg>
                                        </a>
                                        <a href="<?php echo BASE_URL ?>/business/contenido/categorias/delete.php?category_id=<?php echo $category["id"]; ?>" class="action delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                                                <path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                            </svg>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="category-content">
                                <a href="<?php echo BASE_URL ?>/business/contenido/categorias?category_id=<?php echo $category["id"]; ?>" class="btn btn-primary">Ver documentos</a>
                            </div>
                        </div>
                <?php }
                } ?>

                <?php if ($_SESSION["role"] === "admin") { ?>
                    <div class="add-more">
                        <a class="btn btn-primary" href="<?php echo BASE_URL . '/business/contenido/categorias/add.php' ?>">Añadir Categoría</a>
                    </div>
                <?php } ?>
            </section>
        </div>
    </section>
</body>

</html>