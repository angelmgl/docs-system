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

$code = isset($_GET["code"]) ? $_GET["code"] : "";
if (strlen($code) !== 15) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

// Preparar la consulta para obtener el codigo de la base de datos.
$stmt = $mydb->prepare("SELECT * FROM password_resets WHERE recovery_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();

$result = $stmt->get_result();
$reset_code = $result->fetch_assoc();
$stmt->close();
$mydb->close();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Cambiar contraseña</title>
    <?php include './components/meta.php' ?>
</head>

<body>
    <main id="login-page" class="content">
        <div class="form-container">
            <h1>Cambiar contraseña</h1>
            <?php if ($reset_code) { ?>
                <p style="max-width: 400px; text-align: center; margin-bottom: 40px;">
                    Establece una nueva contraseña para continuar.
                </p>
                <form id="login-form" action="./actions/change_password.php" method="POST">
                    <div class="fields-container">
                        <input type="hidden" name="code" value="<?php echo $code ?>" />

                        <input class="custom-input" type="password" placeholder="Nueva contraseña" name="password" id="password" required />
                        <input class="custom-input" type="password" placeholder="Repetir contraseña" name="password_repeat" id="password_repeat" required />
                        <label class="cursor-pointer" for="show-password" style="font-size: 14px; display: block; margin-bottom: 10px;">
                            <input type="checkbox" id="show-password">
                            Mostrar contraseña
                        </label>
                    </div>
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<p class="error" style="max-width: 300px;">';
                        echo $_SESSION['error'];
                        echo '</p>';
                        unset($_SESSION['error']);
                    }
                    ?>
                    <input id="submit-btn" class="btn btn-primary disabled" type="submit" value="Cambiar contraseña" disabled />
                    <p style="max-width: 300px;" id="pw-message">
                        No olvides que una contraseña segura tiene al menos 8 caracteres.
                    </p>
                    <hr>
                    <a href="<?php echo BASE_URL ?>/login.php" class="link">Volver a iniciar sesión</a>
                </form>
            <?php } else { ?>
                <p>Enlace inválido o expirado, consigue uno nuevo.</p>
                <a href="<?php echo BASE_URL . '/forgot-password.php' ?>" class="btn btn-primary">Conseguir nuevo</a>
            <?php } ?>
        </div>
    </main>

    <script src="<?php echo BASE_URL ?>/assets/js/password.js"></script>
</body>

</html>