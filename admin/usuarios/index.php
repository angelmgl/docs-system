<?php

require '../../config/config.php';
require '../../helpers/dates.php';
require '../../helpers/users.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// filtros
$business_id_value = isset($_GET['business_id']) ? $_GET['business_id'] : '';
$full_name_value = isset($_GET['full_name']) ? htmlspecialchars($_GET['full_name']) : '';
$role_value = isset($_GET['role']) ? $_GET['role'] : '';

$sql = "SELECT * FROM users WHERE 1=1";

if ($full_name_value) {
    $sql .= " AND full_name LIKE ?";
    $full_name_value = "%$full_name_value%";
}
if ($role_value) {
    $sql .= " AND role = ?";
}
if ($business_id_value !== '') {
    $sql .= " AND business_id = ?";
}

$stmt = $mydb->prepare($sql);

$params = [];
$types = '';
if ($full_name_value) {
    $types .= 's';
    $params[] = &$full_name_value;
}
if ($role_value) {
    $types .= 's';
    $params[] = &$role_value;
}
if ($business_id_value !== '') {
    $types .= 'i';
    $params[] = &$business_id_value;
}
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$stmt->close();

// consulta de businesses
$b_stmt = $mydb->prepare("SELECT * FROM businesses WHERE is_active = 1");
$b_stmt->execute();
$result = $b_stmt->get_result();

$businesses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

$b_stmt->close();

$full_name_value_display = str_replace('%', '', $full_name_value);

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

        <form method="GET" class="custom-form filters-container grid cols-4">
            <!-- buscador por nombre -->
            <div class="input-wrapper text-input">
                <label for="full_name">Nombre completo: <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" value="<?php echo $full_name_value_display; ?>">
            </div>
            <!-- filtrar por empresa -->
            <div class="input-wrapper select-input">
                    <label for="business_id">Seleccionar empresa:</label>
                    <select id="business_id" name="business_id">
                        <option value="">Selecciona...</option>
                        <?php foreach ($businesses as $business) { ?>
                            <option value="<?php echo $business["id"] ?>" <?php echo $business_id_value == $business['id'] ? 'selected' : '' ?>>
                                <?php echo $business["name"] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <!-- filtrar por rol -->
            <div class="input-wrapper select-input">
                <label for="role">Seleccionar rol:</label>
                <select id="role" name="role">
                    <option value="" <?php echo ($role_value == '') ? 'selected' : ''; ?>>Selecciona...</option>
                    <option value="super" <?php echo ($role_value == 'super') ? 'selected' : ''; ?>>Super Administrador</option>
                    <option value="admin" <?php echo ($role_value == 'admin') ? 'selected' : ''; ?>>Administrador de Empresa</option>
                    <option value="analyst" <?php echo ($role_value == 'analyst') ? 'selected' : ''; ?>>Analista de Empresa</option>
                </select>
            </div>
            <!-- buscar -->
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>

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