<?php

require '../../config/config.php';
require '../../helpers/dates.php';
require '../../helpers/users.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['superadmin']);

$sql = "SELECT * FROM users";
$stmt = $mydb->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$mydb->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Usuarios</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/admin/header.php'; ?>
    <section class="container py px">
        <div class="admin-bar">
            <h1>Administrar Usuarios</h1>
            <a class="btn btn-primary" href="<?php echo BASE_URL ?>/admin/usuarios/add.php">Añadir usuario</a>
        </div>

        <?php
        if (isset($_SESSION['success'])) {
            echo '<p class="success">';
            echo $_SESSION['success'];
            echo '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <?php if (empty($users)) { ?>
            <p>No hay resultados para esta búsqueda...</p>
        <?php } else { ?>
            <section class="users-grid">
                <?php
                foreach ($users as $user) {
                    include '../../components/admin/user_card.php';
                }
                ?>
            </section>
        <?php } ?>
    </section>
</body>

</html>