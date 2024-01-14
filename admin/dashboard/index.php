<?php

require '../../config/config.php';
require '../../helpers/auth.php';
require '../../helpers/dates.php';
require '../../helpers/business.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard</title>
    <?php include '../../components/meta.php' ?>
</head>

<body>
    <?php include '../../components/admin/header.php' ?>
    <section class="container py px content">
        <h1>Bienvenido <?php echo $_SESSION['full_name'] ?></h1>
        <p>
            @<?php echo $_SESSION['username'] . " - Super Administrador" ?>
        </p>

        <div class="grid cols-2" style="align-items: start;">
            <?php include('./panels/businesses_near_expiration.php') ?>
            <?php include('./panels/expired_businesses.php') ?>
            <?php include('./panels/users_per_business.php') ?>
            <?php include('./panels/docs_per_business.php') ?>
            <?php include('./panels/users_per_last_login.php') ?>
        </div>
    </section>
</body>

</html>