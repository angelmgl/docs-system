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

$role = isset($_SESSION['this_role']) ? $_SESSION['this_role'] : '';

$heading = $role === 'admin' ? "Su empresa se encuentra inactiva por falta de pago" : "Ocurrió un error";
$admin_message = "Su suscripción a " . APP_NAME . " ha expirado. No se preocupe, sus datos están a salvo. Para continuar usando esta aplicación, necesita ponerse en contacto con el soporte de la plataforma y reanudar su suscripción.";
$analyst_message = "Parece que hay problemas con el negocio al que intentas ingresar. Ponte en contacto con uno de tus superiores para solucionar el problema.";
$message = $role === 'admin' ? $admin_message : $analyst_message;

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
            <h1><?php echo $heading ?></h1>
            <p style="max-width: 500px; text-align: center;"><?php echo $message ?></p>
            <div style="margin-top: 12px;">
                <?php if ($role === 'admin') { ?>
                    <a href="<?php echo CONTACT_URL ?>" class="btn btn-primary" style="margin-right: 12px;" target="_blank">Contactar soporte</a>
                <?php } ?>
                <a href="<?php echo BASE_URL . '/login.php' ?>" class="btn btn-secondary">Regresar</a>
            </div>
        </div>
    </main>
</body>

</html>