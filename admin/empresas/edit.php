<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/business.php';
require '../../helpers/roles.php';

// iniciar sesión y verificar autorización
session_start();

verifyRole('superadmin');

$business_id = $_GET["id"];

$business = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $business_id);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si 'expiration_date' es null o no está definido, usamos la fecha actual. Si no, usamos su valor.
$expiration_date = empty($business['expiration_date']) ? date("Y-m-d") : $business['expiration_date'];

// Si no se encontró a la empresa, redirige a la página de lista de empresas.
if ($business === null) {
    header("Location: " . BASE_URL . "/admin/empresas.php");
    exit;
}
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
            <form class="custom-form" action="./actions/update_business.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="business_id" name="business_id" value="<?php echo $business['id']; ?>">
                <input type="hidden" id="old_photo" name="old_photo" value="<?php echo $business['logo']; ?>">

                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="name">Nombre: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo $business['name']; ?>" required>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper date-input">
                        <label for="expiration_date">Fecha de expiración:</label>
                        <div class="date-field">
                            <input type="date" id="expiration_date" name="expiration_date" value="<?php echo $expiration_date; ?>">
                            <button type="button" id="extend-30-days">+30</button>
                        </div>
                    </div>

                    <div class="input-wrapper checkbox-input">
                        <label for="is_active">Activo:</label>
                        <input type="checkbox" id="is_active" name="is_active" <?php echo ($business['is_active'] == 1) ? 'checked' : ''; ?>>
                    </div>

                    <?php include '../../components/admin/logo_field.php' ?>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar empresa">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/businesses.js"></script>
    <script src="<?php echo BASE_URL ?>/assets/js/expiration_date.js"></script>
</body>

</html>