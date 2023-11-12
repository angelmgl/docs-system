<?php

require '../../config/config.php';

// iniciar sesión y verificar autorización
session_start();

if ($_SESSION['role'] !== 'superadmin') {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

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
        <?php } else { ?>
            <ul>
                <?php
                foreach ($businesses as $business) { ?>
                    <li><?php echo $business["name"] ?></li>
                <?php }
                ?>
            </ul>
        <?php } ?>
    </section>
</body>

</html>