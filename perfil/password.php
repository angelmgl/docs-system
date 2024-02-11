<?php

require '../config/config.php';
require '../helpers/auth.php';
$title = "Cambiar contraseña";

// iniciar sesión y verificar autorización
session_start();

verifyAuthentication();

$user = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);

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
    header("Location: " . BASE_URL . '/perfil');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include '../components/meta.php'; ?>
</head>

<body>
    <?php 
        if($_SESSION['role'] === 'super') {
            include '../components/admin/header.php';
        } else {
            include '../components/business/header.php';
        }
    ?>

    <main class="container px py">
        <div class="admin-bar">
            <h1>Cambiar mi contraseña</h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL . '/perfil'?>">Volver</a>
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
            <form class="custom-form" action="./actions/update_password.php" method="POST" enctype="multipart/form-data">
                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="password">Nueva contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="password_repeat">Repetir contraseña:</label>
                        <input type="password" id="password_repeat" name="password_repeat" required>
                    </div>

                    <label class="cursor-pointer" for="show-password">
                        <input type="checkbox" id="show-password">
                        Mostrar contraseña
                    </label>

                    <p>
                        No olvides que una contraseña segura tiene al menos 8 caracteres.
                    </p>
                </div>
                <div class="manage-section">
                    <input id="submit-btn" class="btn btn-primary disabled" type="submit" value="Actualizar contraseña" disabled>
                </div>
            </form>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/password.js"></script>
</body>

</html>