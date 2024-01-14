<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/business.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar categoría</title>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/business/header.php'; ?>
    <main class="container py px content">
        <div class="admin-bar">
            <h1>Agregar categoría</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/business/contenido">Regresar</a>
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
                        <textarea rows="4" id="description" name="description"><?php echo get_form_data('description'); ?> </textarea>
                    </div>
                </div>

                <div class="manage-section">
                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Crear Categoría">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>