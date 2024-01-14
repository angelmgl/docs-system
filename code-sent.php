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

$email = isset($_GET["email"]) ? $_GET["email"] : "";
if (strlen($email) <= 0) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>¡Enlace enviado!</title>
    <?php include './components/meta.php' ?>
</head>

<body>
    <main id="login-page" class="content">
        <div class="form-container">
            <h1>¡Enlace enviado!</h1>
            <p style="max-width: 450px;">
                Revisa la bandeja de entrada de tu correo <span class="link"><?php echo $email ?></span>, hemos enviado un enlace para que puedas
                establecer una nueva contraseña. El enlace será válido por 1 hora.
            </p>
            <p style="max-width: 450px;">
                Dependiendo de la configuración de tu correo electrónico, a veces podría llegar a la carpeta de SPAM. Sino lo 
                recibes en 5 minutos, vuelve a solicitar <a class="link" href="<?php echo BASE_URL ?>/forgot-password.php">un nuevo enlace aquí.</a>
            </p>
        </div>
    </main>
</body>

</html>