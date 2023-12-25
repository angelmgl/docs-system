<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();
verifyRoles(['admin']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar usuarios en bloque</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/business/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Agregar usuarios en bloque</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/business/usuarios">Regresar</a>
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
            <form class="custom-form" action="./actions/create_users.php" method="POST" enctype="multipart/form-data">
                <div class="data-section">
                    <p>
                        Para crear usuarios en bloque, necesitas completar un archivo CSV con las columnas: <strong>username, full_name, email y password.</strong>
                        Puedes crear las tablas en cualquier Excel o SpreadSheet y luego descargar como CSV.
                        <a class="change-password" href="<?php echo BASE_URL ?>/assets/img/csv_example.png" target="_blank">Ver ejemplo</a>
                    </p>

                    <hr>

                    <div class="input-wrapper text-input">
                        <label for="csv_file">Archivo CSV: <span class="required">*</span></label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper select-input">
                    <p>
                        Por razones de seguridad, todos los usuarios creados en bloque se crean con rol <strong>Analista de Empresa</strong>.
                    </p>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Crear en bloque">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
</body>

</html>