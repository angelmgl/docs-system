<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/business.php';
require '../../helpers/users.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

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

// preparar la consulta
$users = [];
$u_stmt = $mydb->prepare("SELECT id, full_name, profile_picture, role FROM users WHERE business_id = ?");
$u_stmt->bind_param("i", $business_id);

// ejecutar la consulta
$u_stmt->execute();

$u_result = $u_stmt->get_result();
while ($row = $u_result->fetch_assoc()) {
    $users[] = $row; // Agrega cada fila al array $users
}

$u_stmt->close();

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
            } else if (isset($_SESSION['success'])) {
                echo '<p class="success">';
                echo $_SESSION['success'];
                echo '</p>';
                unset($_SESSION['success']);
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

                    <hr />

                    <h2>Administradores</h2>

                    <?php foreach ($users as $user) { ?>
                        <div class="admin-card">
                            <div class="admin-picture" style="background-image: url(<?php echo get_profile_picture($user) ?>)"></div>
                            <span class="admin-name"><?php echo $user["full_name"] ?></span>
                            <span class="admin-role"><?php echo get_role($user); ?></span>
                            <div class="admin-actions">
                                <a href="<?php echo BASE_URL ?>/admin/empresas/edit-user.php?business_id=<?php echo $business["id"]; ?>&user_id=<?php echo $user["id"]; ?>" class="action edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                        <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                    </svg>
                                </a>
                                <a href="<?php echo BASE_URL ?>/admin/empresas/delete-user.php?business_id=<?php echo $business["id"]; ?>&user_id=<?php echo $user["id"]; ?>" class="action delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                                        <path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                    <a href="<?php echo BASE_URL ?>/admin/empresas/add-user.php?business_id=<?php echo $business_id ?>" class="btn btn-primary">Agregar nuevo</a>
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