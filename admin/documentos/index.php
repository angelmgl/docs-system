<?php

require '../../config/config.php';
require '../../helpers/roles.php';

// iniciar sesión y verificar autorización
session_start();

verifyRole('superadmin');

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard</title>
    <?php include '../../components/meta.php' ?>
</head>

<body>
    <?php include '../../components/admin/header.php' ?>
    <section class="container py px">
        <h1>Administrar Documentos</h1>
    </section>
</body>

</html>