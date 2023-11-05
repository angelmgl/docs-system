<?php

require '../config/config.php';

// iniciar sesión y verificar autorización
session_start();

if ($_SESSION['role'] !== 'superadmin') {
    header("Location: " . BASE_URL);
    exit;
}

?>

<h1>Bienvenido al App Admin</h1>
<p>
    <?php echo $_SESSION['username'] . " - " . $_SESSION['role'] ?>
</p>

<form action="<?php echo BASE_URL ?>/actions/auth_logout.php" method="post">
    <button class="btn btn-primary" type="submit" name="logout">Cerrar sesión</button>
</form>