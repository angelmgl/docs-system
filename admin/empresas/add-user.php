<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$business_id = $_GET["business_id"];

$business = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->bind_param("i", $business_id);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
}

$stmt->close();

// Si no se encontró a la empresa, redirige a la página de lista de empresas.
if ($business === null) {
    $mydb->close();
    header("Location: " . BASE_URL . "/admin/empresas");
    exit;
}

// Consulta para obtener todos los roles
$roles = [];
$rolesStmt = $mydb->prepare("SELECT * FROM roles");
$rolesStmt->execute();
$rolesResult = $rolesStmt->get_result();

while ($row = $rolesResult->fetch_assoc()) {
    $roles[] = $row;
}

$rolesStmt->close();

// Consulta para obtener todos los usuarios activos que no son superusuarios
$users = [];
$usersStmt = $mydb->prepare("SELECT id, full_name, username FROM users WHERE is_active = TRUE AND is_superuser = FALSE");
$usersStmt->execute();
$usersResult = $usersStmt->get_result();

while ($userRow = $usersResult->fetch_assoc()) {
    $users[] = $userRow;
}

$usersStmt->close();

$assignedUsers = [];
$assignedUsersStmt = $mydb->prepare("SELECT user_id FROM roles_businesses WHERE business_id = ?");
$assignedUsersStmt->bind_param("i", $business_id);
$assignedUsersStmt->execute();
$assignedUsersResult = $assignedUsersStmt->get_result();

while ($row = $assignedUsersResult->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

$assignedUsersStmt->close();

$mydb->close();

$filteredUsers = array_filter($users, function ($user) use ($assignedUsers) {
    return !in_array($user['id'], $assignedUsers);
});
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Agregar usuario a <?php echo $business["name"]; ?></title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Agregar usuario a <?php echo $business["name"] ?></h1>
            <a class="btn btn-secondary" href="<?php echo BASE_URL ?>/admin/empresas/edit.php?id=<?php echo $business_id ?>">Regresar</a>
        </div>

        <section>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">';
                echo $_SESSION['error'];
                echo '</p>';
                unset($_SESSION['error']);
            }
            ?>
            <form class="custom-form" action="./actions/add_user.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="business_id" name="business_id" value="<?php echo $business['id']; ?>">

                <div class="data-section">
                    <p>
                        Estás a punto de agregar un usuario a la empresa: <strong><?php echo $business["name"] ?></strong>. Asegurate
                        de elegir correctamente al usuario y al rol.
                    </p>
                    <div class="grid cols-2">
                        <div class="input-wrapper select-input">
                            <label for="user_id">Seleccionar usuario:</label>
                            <select id="user_id" name="user_id">
                                <?php foreach ($filteredUsers as $user) { ?>
                                    <option value="<?php echo $user["id"] ?>"><?php echo $user["full_name"] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="input-wrapper select-input">
                            <label for="role_id">Seleccionar rol:</label>
                            <select id="role_id" name="role_id">
                                <?php foreach ($roles as $role) { ?>
                                    <option value="<?php echo $role["id"] ?>"><?php echo $role["name"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="manage-section">
                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Agregar">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>