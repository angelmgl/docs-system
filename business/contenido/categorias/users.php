<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$category_id = $_GET["category_id"];
$business_id = $_SESSION["business_id"];

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
$user_stmt = $mydb->prepare("SELECT * FROM users WHERE business_id = ? AND role = ?");
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Otorgar acceso</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/business/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Otorgar acceso a la categoría <?php echo $category["name"] ?></h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/business/contenido/categorias/edit.php?category_id=<?php echo $category["id"] ?>">Regresar</a>
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
            <form class="custom-form" action="./actions/assign_users.php" method="POST">
                <input type="hidden" id="category_id" name="category_id" value="<?php echo $category['id']; ?>">

                <div class="data-section">
                    <h2>Analistas de la empresa:</h2>
                    <?php if (empty($users)) { ?>
                        <p>
                            Esta empresa no tiene analistas...
                            <a class="change-password" href="<?php echo BASE_URL ?>/admin/usuarios/add.php">Agregar usuarios</a>
                        </p>
                    <?php } ?>
                    <?php foreach ($users as $user) { ?>
                        <div class="input-wrapper checkbox-input">
                            <input type="checkbox" id="user_<?php echo $user["username"]; ?>" name="user_<?php echo $user["id"]; ?>" <?php echo in_array($user["id"], $assigned_user_ids) ? 'checked' : ''; ?>>
                            <label for="user_<?php echo $user["username"]; ?>"><?php echo $user["full_name"]; ?></label>
                        </div>
                    <?php } ?>
                </div>

                <div class="manage-section">
                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar permisos">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>