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
    <title>Recuperar contraseña</title>
    <?php include './components/meta.php' ?>
</head>

<body>
    <main id="login-page" class="content">
        <div class="form-container">
            <h1>Recuperar contraseña</h1>
            <p style="max-width: 400px; text-align: center; margin-bottom: 40px;">
                Vamos a enviarte un correo electrónico con un enlace para recuperar tu contraseña. Proporciona 
                la dirección de email asociada a tu cuenta, y espera unos minutos.
            </p>
            <form id="login-form" action="./actions/recover_password.php" method="POST">
                <div class="fields-container">
                    <input class="custom-input" type="email" placeholder="Correo electrónico" name="email" required />
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error" style="max-width: 300px;">';
                    echo $_SESSION['error'];
                    echo '</p>';
                    unset($_SESSION['error']);
                }
                ?>
                <input class="btn btn-primary" type="submit" value="Recibir código" />
                <hr>
                <a href="<?php echo BASE_URL ?>/login.php" class="link">Volver a iniciar sesión</a>
            </form>
        </div>
    </main>
</body>

</html>