<?php

require '../../config/config.php';
require '../../helpers/auth.php';
require '../../helpers/business.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$business_id = isset($_GET["business_id"]) ? (int) $_GET["business_id"] : 0;

$stmt = $mydb->prepare("SELECT * FROM businesses WHERE is_active = 1");
$stmt->execute();
$result = $stmt->get_result();

$businesses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

$stmt->close();

$categories = [];
$selected_business = null;

if ($business_id) {
    foreach ($businesses as $business) {
        if ($business['id'] == $business_id) {
            $selected_business = $business;
            break; // Rompe el bucle una vez que encuentres el negocio correspondiente
        }
    }

    $cat_sql = "SELECT * FROM businesses";
    $cat_stmt = $mydb->prepare("SELECT * FROM categories WHERE business_id = ?");
    $cat_stmt->bind_param("i", $business_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();

    if ($cat_result->num_rows > 0) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    $cat_stmt->close();
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
    <?php include '../../components/admin/header.php' ?>
    <section class="container py px">
        <div class="admin-bar">
            <h1>Administrar Contenido</h1>

            <form class="custom-form filter">
                <div class="input-wrapper select-input">
                    <label for="business_id">Seleccionar empresa:</label>
                    <select id="business_id" name="business_id">
                        <?php foreach ($businesses as $business) { ?>
                            <option value="<?php echo $business["id"] ?>" <?php echo $business_id === $business['id'] ? 'selected' : '' ?>>
                                <?php echo $business["name"] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button class="btn btn-primary">Seleccionar</button>
            </form>
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
            <?php if ($business_id === 0) { ?>
                <p>Selecciona una empresa para ver su contenido...</p>
            <?php } else { ?>
                <div class="my-business-card">
                    <div class="logo" style="background-image: url(<?php echo get_logo($selected_business) ?>)"></div>
                    <div class="my-business-content">
                        <p class="my-business-name"><?php echo $selected_business["name"] ?></p>
                    </div>
                </div>

                <section style="margin-top: 40px">
                    <?php if (empty($categories)) { ?>
                        <p>Esta empresa no tiene categorías creadas aún...</p>
                        <?php } else {
                        foreach ($categories as $category) { ?>
                            <div class="category-item">
                                <div class="category-header">
                                    <h2><?php echo $category["name"] ?></h2>
                                    <p><?php echo $category["description"] ?></p>
                                    <a href="<?php echo BASE_URL ?>/admin/contenido/categorias/edit.php?category_id=<?php echo $category["id"]; ?>" class="action edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="category-content">
                                    <p>contenido de esta categoría...</p>
                                </div>
                            </div>
                    <?php }
                    } ?>

                    <div class="add-more">
                        <a class="btn btn-primary" href="<?php echo BASE_URL . '/admin/contenido/categorias/add.php?business_id=' . $business_id ?>">Añadir Categoría</a>
                    </div>
                </section>
            <?php } ?>
        </div>
    </section>
</body>

</html>