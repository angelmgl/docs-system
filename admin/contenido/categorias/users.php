<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/users.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$category_id = $_GET["category_id"];
$business_id = $_GET["business_id"];

$category = null;

// preparar la consulta
$cat_stmt = $mydb->prepare("SELECT * FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);

// ejecutar la consulta
$cat_stmt->execute();

$cat_result = $cat_stmt->get_result();
if ($cat_result->num_rows > 0) {
    $category = $cat_result->fetch_assoc();
}

$cat_stmt->close();

// Si no se encontró a la categoría, redirige a la página de lista de categorías.
if ($category === null) {
    header("Location: " . BASE_URL . "/admin/contenido");
    exit;
}

// Consulta de usuarios de la misma empresa
$role = "analyst";

// Preparar la consulta
$user_stmt = $mydb->prepare("SELECT full_name, id, profile_picture, username FROM users WHERE business_id = ? AND role = ? AND is_active = 1");
$user_stmt->bind_param("is", $business_id, $role);

// Ejecutar la consulta
$user_stmt->execute();

$user_result = $user_stmt->get_result();
$users = []; // Array para almacenar los usuarios
if ($user_result->num_rows > 0) {
    while ($row = $user_result->fetch_assoc()) {
        $users[] = $row; // Agregar cada usuario al array
    }
}

$user_stmt->close();

// consulta para obtener los usuarios asignados
$assigned_users_stmt = $mydb->prepare("SELECT user_id FROM user_categories WHERE category_id = ?");
$assigned_users_stmt->bind_param("i", $category_id);
$assigned_users_stmt->execute();
$assigned_users_result = $assigned_users_stmt->get_result();

$assigned_user_ids = [];
while ($assigned_row = $assigned_users_result->fetch_assoc()) {
    $assigned_user_ids[] = $assigned_row['user_id'];
}

$assigned_users_stmt->close();

$mydb->close();

$assigned_users = [];
$unassigned_users = [];

foreach ($users as $user) {
    if (in_array($user['id'], $assigned_user_ids)) {
        // El usuario está asignado
        $assigned_users[] = $user;
    } else {
        // El usuario no está asignado
        $unassigned_users[] = $user;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Otorgar acceso</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Otorgar acceso a la categoría <?php echo $category["name"] ?></h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/contenido/categorias/edit.php?category_id=<?php echo $category["id"] ?>">Regresar</a>
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
            <div class="custom-form">

                <div class="data-section">
                    <?php if (empty($users)) { ?>
                        <p>
                            Esta empresa no tiene analistas...
                            <a class="change-password" href="<?php echo BASE_URL ?>/admin/usuarios/add.php">Agregar usuarios</a>
                        </p>
                    <?php } ?>

                    <?php if (!empty($assigned_users)) { ?>
                        <h2>Analistas con acceso a esta categoría:</h2>
                    <?php } ?>

                    <?php foreach ($assigned_users as $user) { ?>
                        <form class="user-mini-form" method="POST" action="./actions/unassign_user.php">
                            <div class="user-info">
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $user['id'] ?>" />
                                <input type="hidden" id="category_id" name="category_id" value="<?php echo $category['id']; ?>">
                                <input type="hidden" id="business_id" name="business_id" value="<?php echo $category['business_id']; ?>">

                                <div class="logo" style="background-image: url(<?php echo get_profile_picture($user) ?>)"></div>
                                <div>
                                    <h4><?php echo $user['full_name'] ?></h4>
                                    <span>@<?php echo $user['username'] ?></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-danger">Remover acceso</button>
                        </form>
                    <?php } ?>

                    <hr />

                    <?php if (!empty($unassigned_users)) { ?>
                        <h2>Analistas sin acceso a esta categoría:</h2>
                    <?php } ?>

                    <?php foreach ($unassigned_users as $user) { ?>
                        <form class="user-mini-form" method="POST" action="./actions/assign_user.php">
                            <div class="user-info">
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $user['id'] ?>" />
                                <input type="hidden" id="category_id" name="category_id" value="<?php echo $category['id']; ?>">
                                <input type="hidden" id="business_id" name="business_id" value="<?php echo $category['business_id']; ?>">

                                <div class="logo" style="background-image: url(<?php echo get_profile_picture($user) ?>)"></div>
                                <div>
                                    <h4><?php echo $user['full_name'] ?></h4>
                                    <span>@<?php echo $user['username'] ?></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Otorgar acceso</button>
                        </form>
                    <?php } ?>

                </div>

                <div class="manage-section">
                    <a href="<?php echo BASE_URL ?>/admin/contenido/categorias/edit.php?category_id=<?php echo $category["id"] ?>" class="btn btn-primary">Finalizar cambios</a>
                </div>
            </div>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>