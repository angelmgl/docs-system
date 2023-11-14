<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/business.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['superadmin']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar empresa</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Agregar empresa</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/empresas">Regresar</a>
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
            <form class="custom-form" action="./actions/create_business.php" method="POST" enctype="multipart/form-data">
                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="name">Nombre: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo get_form_data('name'); ?>" required>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper checkbox-input">
                        <label for="is_active">Activo:</label>
                        <input type="checkbox" id="is_active" name="is_active" checked>
                    </div>

                    <?php include '../../components/admin/logo_field.php' ?>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Crear Empresa">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>