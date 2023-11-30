<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/business.php';
require '../../../helpers/auth.php';

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
$mydb->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar categoría</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Agregar categoría</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/contenido">Regresar</a>
        </div>

        <section>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">';
                echo $_SESSION['error'];
                echo '</p>';
                unset($_SESSION['error']);
            }
            ?>
            <form class="custom-form" action="./actions/create_category.php" method="POST">
                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="name">Nombre: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo get_form_data('name'); ?>" required>
                    </div>
                    
                    <div class="input-wrapper text-input">
                        <label for="description">Descripción:</label>
                        <textarea rows="4" id="description" name="description" value="<?php echo get_form_data('description'); ?>"></textarea>
                    </div>
                </div>

                <div class="manage-section">
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

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Crear Categoría">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>