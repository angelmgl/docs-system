<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/auth.php';
require '../../helpers/business.php';
require '../../helpers/users.php';

// iniciar sesión y verificar autorización
session_start();
verifyRoles(['admin', 'analyst']);

$my_business = $_SESSION['business_id'];

$sql = "SELECT * FROM businesses WHERE id = $my_business";
$stmt = $mydb->prepare($sql);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
}

$stmt->close();

// consulta de usuarios
$u_sql = "SELECT profile_picture, full_name, role FROM users WHERE business_id = $my_business AND is_active = 1";
$u_stmt = $mydb->prepare($u_sql);

$u_stmt->execute();
$u_result = $u_stmt->get_result();

$users = [];
if ($u_result->num_rows > 0) {
    while ($row = $u_result->fetch_assoc()) {
        $users[] = $row;
    }
}

$u_stmt->close();

$mydb->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar usuario</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/business/header.php'; ?>
    <main class="container px py">
        <div class="my-business-card">
            <div class="logo" style="width: 120px; height: 120px; background-image: url(<?php echo get_logo($business) ?>)"></div>
            <div class="my-business-content">
                <h1><?php echo $business["name"] ?></h1>
            </div>
        </div>

        <h2 style="margin-top: 80px">Miembros</h2>
        <div class="users-list">
            <?php foreach ($users as $user) { ?>
                <div class="user-item">
                    <div class="logo" style="width: 80px; height: 80px; background-image: url(<?php echo get_profile_picture($user) ?>)"></div>
                    <h3><?php echo $user["full_name"] ?></h3>
                    <p><?php echo get_role($user) ?></p>
                </div>
            <?php } ?>
        </div>
    </main>
</body>

</html>