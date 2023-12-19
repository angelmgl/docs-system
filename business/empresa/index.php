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
    <title><?php echo $business["name"] ?></title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/business/header.php'; ?>
    <main class="container px py">
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="error">';
            echo $_SESSION['error'];
            echo '</p>';
            unset($_SESSION['error']);
        } else if (isset($_SESSION['success'])) {
            echo '<p class="success">';
            echo $_SESSION['success'];
            echo '</p>';
            unset($_SESSION['success']);
        }
        ?>
        <div class="my-business-card">
            <div class="logo" style="width: 120px; height: 120px; background-image: url(<?php echo get_logo($business) ?>)"></div>
            <div class="my-business-content">
                <h1><?php echo $business["name"] ?></h1>
                <?php if ($_SESSION["role"] === "admin") { ?>
                    <a class="edit-business" href="<?php echo BASE_URL ?>/business/empresa/edit.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                        </svg>
                    </a>
                <?php } ?>
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