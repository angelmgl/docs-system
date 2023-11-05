<?php

require './config/config.php';
session_start();

// Si el usuario ya tiene una sesión iniciada y es admin redirige al dashboard
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == "superadmin") {
        header("Location: " . BASE_URL . "/admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == "admin" || $_SESSION['role'] == "analyst") {
        header("Location: " . BASE_URL . "/business/dashboard.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
</head>

<body>
    <main id="login-page">
        <div class="form-container">
            <h1>Inicia sesión</h1>
            <form id="login-form" action="./actions/auth_login.php" method="POST">
                <div class="fields-container">
                    <input class="custom-input" type="text" placeholder="Nombre de usuario" name="username" required />
                    <input class="custom-input" type="password" placeholder="Contraseña" name="password" required />
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error">';
                    echo $_SESSION['error'];
                    echo '</p>';
                    unset($_SESSION['error']);
                }
                ?>
                <input class="btn btn-primary" type="submit" value="Ingresar" />
            </form>
        </div>
    </main>
</body>

</html>