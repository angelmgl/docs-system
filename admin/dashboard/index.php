<?php

require '../../config/config.php';

// iniciar sesión y verificar autorización
session_start();

if ($_SESSION['role'] !== 'superadmin') {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <?php include '../../components/meta.php' ?>
</head>

<body>
    <?php include '../../components/admin/header.php' ?>
    <section class="container py px">
        <h1>Bienvenido al App Admin</h1>
        <p>
            <?php echo $_SESSION['username'] . " - " . $_SESSION['role'] ?>
        </p>
    </section>
</body>

</html>