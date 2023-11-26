<?php

require '../../config/config.php';
require '../../helpers/forms.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

$business_id = $_GET["business_id"];
$user_id = $_GET["user_id"];

$business = null;

// preparar la consulta
$businessStmt = $mydb->prepare("SELECT * FROM businesses WHERE id = ?");
$businessStmt->bind_param("i", $business_id);

// ejecutar la consulta
$businessStmt->execute();

$result = $businessStmt->get_result();
if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
}

$businessStmt->close();

// Si no se encontró a la empresa, redirige a la página de lista de empresas.
if ($business === null) {
    $mydb->close();
    header("Location: " . BASE_URL . "/admin/empresas.php");
    exit;
}

$user = null;

// preparar la consulta
$userStmt = $mydb->prepare("
    SELECT * FROM users WHERE id = ? AND business_id = ?
");
$userStmt->bind_param("ii", $user_id, $business_id);

// ejecutar la consulta
$userStmt->execute();

$result = $userStmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

$userStmt->close();

// Si no se encontró al usuario, redirige a la página de la empresas.
if ($user === null) {
    $mydb->close();
    header("Location: " . BASE_URL . "/admin/empresas/edit.php?id=" . $business_id);
    exit;
}

$mydb->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar usuario de <?php echo $business["name"]; ?></title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/admin/header.php'; ?>
    <main class="container py px">
        <div class="admin-bar">
            <h1>Editar usuario de <?php echo $business["name"]; ?></h1>
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
            <form class="custom-form" action="./actions/update_user.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="business_id" name="business_id" value="<?php echo $business['id']; ?>">

                <div class="data-section">
                    <p>
                        Estás a punto de editar el rol de <strong><?php echo $user["full_name"] ?></strong> en la empresa:
                        <strong><?php echo $business["name"] ?></strong>. Selecciona el rol que deseas que ocupe ahora:
                    </p>
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
                    <input type="hidden" name="business_id" value="<?php echo $business_id ?>" />

                    <div class="grid cols-2">
                        <div class="input-wrapper text-input">
                            <label>Usuario:</label>
                            <input type="text" class="custom-input" value="<?php echo $user["full_name"] ?>" disabled />
                        </div>

                        <div class="input-wrapper select-input">
                            <label for="role">Seleccionar rol:</label>
                            <select id="role" name="role">
                                <option value="admin" <?php echo $user["role"] == "admin" ? "selected" : "" ?>>Administrador</option>
                                <option value="analyst" <?php echo $user["role"] == "analyst" ? "selected" : "" ?>>Analista</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="manage-section">
                    <input id="submit-btn" class="btn btn-primary" type="submit" value="Actualizar">
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>