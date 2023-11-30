<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$category_id = $_GET["category_id"];

$category = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM categories WHERE id = ?");
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
    header("Location: " . BASE_URL . "/admin/empresas.php");
    exit;
}

// consulta de negocios
$business_stmt = $mydb->prepare("SELECT * FROM businesses WHERE is_active = 1");
$business_stmt->execute();
$business_result = $business_stmt->get_result();

$businesses = [];
if ($business_result->num_rows > 0) {
    while ($row = $business_result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

$business_stmt->close();

$mydb->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar categoría</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Editar categoría</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/empresas">Regresar</a>
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
            <form class="custom-form" action="./actions/update_category.php" method="POST">
                <input type="hidden" id="category_id" name="category_id" value="<?php echo $category['id']; ?>">

                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="name">Nombre: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo $category['name']; ?>" required>
                    </div>
                    
                    <div class="input-wrapper text-input">
                        <label for="description">Descripción:</label>
                        <textarea rows="4" id="description" name="description"><?php echo $category['description']; ?></textarea>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper select-input">
                        <label for="business_id">Seleccionar empresa:</label>
                        <select id="business_id" name="business_id">
                            <?php foreach ($businesses as $business) { ?>
                                <option value="<?php echo $business["id"] ?>" <?php echo $category['business_id'] === $business['id'] ? 'selected' : '' ?>>
                                    <?php echo $business["name"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Crear Categoría">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>