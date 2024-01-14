<?php

require './config/config.php';
session_start();

// Si el usuario ya tiene una sesión iniciada y es admin redirige al dashboard
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == "super") {
        header("Location: " . BASE_URL . "/admin/dashboard");
        exit;
    } elseif ($_SESSION['role'] == "admin" || $_SESSION['role'] == "analyst") {
        header("Location: " . BASE_URL . "/business/dashboard");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Iniciar sesión</title>
    <?php include './components/meta.php' ?>
</head>

<body>
    <main id="login-page">
        <div class="form-container">
            <img class="app-logo" alt="Analytico" src="<?php echo BASE_URL ?>/assets/img/analytico.svg" />
            <h1>Inicia sesión</h1>
            <form id="login-form" action="./actions/auth_login.php" method="POST">
                <div class="fields-container">
                    <input class="custom-input" type="text" placeholder="Nombre de usuario" name="username" required />
                    <input class="custom-input" type="password" placeholder="Contraseña" name="password" required />
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error" style="max-width: 300px;">';
                    echo $_SESSION['error'];
                    echo '</p>';
                    unset($_SESSION['error']);
                } else if (isset($_SESSION['success'])) {
                    echo '<p class="success" style="max-width: 300px;">';
                    echo $_SESSION['success'];
                    echo '</p>';
                    unset($_SESSION['success']);
                }
                ?>
                <input class="btn btn-primary" type="submit" value="Ingresar" />
                <hr>
                <a href="<?php echo BASE_URL ?>/forgot-password.php" class="link">Olvidé mi contraseña</a>
            </form>
        </div>
    </main>
</body>

</html>