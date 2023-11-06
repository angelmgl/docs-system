<?php

require '../../config/config.php';
require '../../helpers/forms.php';

// iniciar sesión y verificar autorización
session_start();

if ($_SESSION['role'] !== 'superadmin') {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar usuario</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Agregar usuario</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/usuarios">Regresar</a>
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
            <form class="custom-form" action="./actions/create_user.php" method="POST" enctype="multipart/form-data">
                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="full_name">Nombre completo: <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo get_form_data('full_name'); ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="email">Email: <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo get_form_data('email'); ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="password">Contraseña: <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <label class="cursor-pointer" for="show-password">
                        <input type="checkbox" id="show-password">
                        Mostrar contraseña
                    </label>

                    <p>
                        No olvides que una contraseña segura tiene al menos 8 caracteres.
                    </p>

                    <div class="input-wrapper text-input">
                        <label for="username">Nombre de usuario: <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo get_form_data('username'); ?>" required>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper checkbox-input">
                        <label for="is_active">Activo:</label>
                        <input type="checkbox" id="is_active" name="is_active" checked>
                    </div>

                    <?php include '../../components/admin/profile_picture_field.php' ?>

                    <input id="submit-btn" class="btn btn-primary disabled" type="submit" value="Crear Usuario" disabled>
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/password.js"></script>
</body>

</html>