<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/users.php';
require '../../helpers/roles.php';

// iniciar sesión y verificar autorización
session_start();

verifyRole('superadmin');

$username = $_GET["username"];

$user = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si no se encontró al usuario, redirige a la página de lista de usuarios.
if ($user === null) {
    header("Location: " . BASE_URL . "/admin/usuarios.php");
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
            <form class="custom-form" action="./actions/update_user.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="user_id" name="user_id" value="<?php echo $user['id']; ?>">
                <input type="hidden" id="old_photo" name="old_photo" value="<?php echo $user['profile_picture']; ?>">

                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="full_name">Nombre completo: <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="email">Email: <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="username">Nombre de usuario: <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                </div>

                <div class="manage-section">
                    <div class="input-wrapper checkbox-input">
                        <label for="is_active">Activo:</label>
                        <input type="checkbox" id="is_active" name="is_active" <?php echo ($user['is_active'] == 1) ? 'checked' : ''; ?>>
                    </div>

                    <?php include '../../components/admin/profile_picture_field.php' ?>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar Usuario">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>