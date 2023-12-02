<?php

require '../config/config.php';
require '../helpers/forms.php';
require '../helpers/users.php';
require '../helpers/business.php';
require '../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();
verifyAuthentication();

$username = $_SESSION["username"];

$user = null;
$stmt = $mydb->prepare("
    SELECT 
        u.full_name, u.profile_picture, u.email, u.username, u.role,
        b.name AS business_name, b.logo AS business_logo
    FROM users u
    LEFT JOIN businesses b ON u.business_id = b.id
    WHERE u.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $user = [
        'full_name' => $row['full_name'],
        'profile_picture' => $row['profile_picture'],
        'email' => $row['email'],
        'username' => $row['username'],
        'role' => $row['role'],
        'business' => $row['business_name'],
        'logo' => $row['business_logo']
    ];
}

$stmt->close();
$mydb->close();

// Si no se encontró al usuario, redirige a la página de lista de usuarios.
if ($user === null) {
    header("Location: " . BASE_URL . "/admin/usuarios.php");
    exit;
}

$return_url = null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Mi perfil</title>
    <?php include '../components/meta.php'; ?>
</head>

<body>
    <?php 
    if($_SESSION['role'] === 'super') {
        include '../components/admin/header.php';
        $return_url = BASE_URL . '/admin/dashboard';
    } else {
        include '../components/business/header.php';
        $return_url = BASE_URL . '/business/dashboard';
    }
    ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Mi perfil</h1>
            <a class="btn btn-secondary" href="<?php echo $return_url ?>">Regresar</a>
        </div>

        <section>
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
            <form class="custom-form" action="./actions/update_profile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="old_photo" name="old_photo" value="<?php echo $user['profile_picture']; ?>">

                <div class="data-section">
                    <div class="input-wrapper text-input">
                        <label for="full_name">Nombre completo: <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="email">Email: <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div class="input-wrapper text-input">
                        <label for="username">Nombre de usuario: <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" disabled>
                    </div>

                    <?php if($user["role"] === "super") { ?>
                        <div class="my-business-card">
                            <h2>Superadministrador de <?php echo APP_NAME ?></h2>
                        </div>
                    <?php } else { ?>
                        <div class="my-business-card">
                            <div class="logo" style="background-image: url(<?php echo get_logo($user) ?>)"></div>
                            <div class="my-business-content">
                                <p class="my-business-name"><?php echo $user["business"] ?></p>
                                <p class="my-business-role"><?php echo $user["role"] ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="manage-section">

                    <?php include '../components/admin/profile_picture_field.php' ?>

                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Guardar datos">

                    <a href="<?php echo BASE_URL . '/perfil/password.php'?>" class="change-password">Cambiar contraseña</a>
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>