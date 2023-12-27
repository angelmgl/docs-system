<?php

require '../../config/config.php';
require '../../helpers/auth.php';
require '../../helpers/dates.php';
require '../../helpers/users.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin', 'analyst']);

$my_business = $_SESSION["business_id"];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard</title>
    <?php include '../../components/meta.php' ?>
</head>

<body>
    <?php include '../../components/business/header.php' ?>
    <section class="container py px">
        <h1>Bienvenido <?php echo $_SESSION['full_name'] ?> al App Admin</h1>
        <p>
            @<?php echo $_SESSION['username'] . " - " . ($_SESSION['role'] === 'admin' ? 'Administrador' : 'Analista') ?>
        </p>

        <div class="grid cols-2" style="align-items: start;">
            <?php include('./panels/business_expiration.php') ?>
            <?php include('./panels/users_count.php') ?>
            <?php include('./panels/docs_count.php') ?>
        </div>
    </section>
</body>

</html>