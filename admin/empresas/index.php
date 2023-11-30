<?php

require '../../config/config.php';
require '../../helpers/business.php';
require '../../helpers/auth.php';
require '../../helpers/dates.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$sql = "SELECT * FROM businesses";
$stmt = $mydb->prepare("SELECT * FROM businesses");
$stmt->execute();
$result = $stmt->get_result();

$businesses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

$stmt->close();
$mydb->close();

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
        <div class="admin-bar">
            <h1>Administrar Empresas</h1>
            <a class="btn btn-primary" href="<?php echo BASE_URL ?>/admin/empresas/add.php">Añadir empresa</a>
        </div>

        <?php
        if (isset($_SESSION['success'])) {
            echo '<p class="success">';
            echo $_SESSION['success'];
            echo '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <?php if (empty($businesses)) { ?>
            <p>No hay resultados para esta búsqueda...</p>
        <?php } else {
            include '../../components/admin/business_table.php';
        } ?>
    </section>
</body>

</html>