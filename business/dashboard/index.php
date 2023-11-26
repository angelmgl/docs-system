<?php

require '../../config/config.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin', 'analyst']);

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
        <h1>Bienvenido al App Business</h1>
        <p>
            <?php echo $_SESSION['username'] . " - " . $_SESSION['role'] . " - " . $_SESSION['business_id'] ?>
        </p>
    </section>
</body>

</html>