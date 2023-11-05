<?php

require('../config/config.php');
session_start();

if (isset($_POST['logout'])) {
    // Elimina todas las variables de sesión
    session_unset();

    // Destruye la sesión
    session_destroy();

    // Redirige al usuario a la página de inicio de sesión
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

?>