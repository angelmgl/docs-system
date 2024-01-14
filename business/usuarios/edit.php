<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/users.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

$username = $_GET["username"];

$user = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM users WHERE username = ? AND business_id = ?");
$stmt->bind_param("si", $username, $my_business);

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
    header("Location: " . BASE_URL . "/business/usuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar usuario</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/business/header.php'; ?>
    <main class="container py px content">
        <div class="admin-bar">
            <h1>Editar usuario</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/business/usuarios">Regresar</a>
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

                    <div class="input-wrapper select-input">
                        <label for="role">Seleccionar rol:</label>
                        <select id="role" name="role" required>
                            <option value="analyst" <?php echo $user['role'] === 'analyst' ? 'selected' : '' ?>>Analista</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>

                    <?php include '../../components/admin/profile_picture_field.php' ?>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar Usuario">

                    <a href="<?php echo BASE_URL . "/business/usuarios/password.php?username=" . $user['username'] ?>" class="change-password">Cambiar contraseña</a>
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>