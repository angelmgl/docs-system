<?php

require '../../config/config.php';
require '../../helpers/dates.php';
require '../../helpers/users.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

// filtros
$full_name_value = isset($_GET['full_name']) ? htmlspecialchars($_GET['full_name']) : '';
$role_value = isset($_GET['role']) ? $_GET['role'] : '';
$is_active_value = isset($_GET['is_active']) ? $_GET['is_active'] : '';

$sql = "SELECT * FROM users WHERE business_id = $my_business";

if ($full_name_value) {
    $sql .= " AND full_name LIKE ?";
    $full_name_value = "%$full_name_value%";
}
if ($role_value) {
    $sql .= " AND role = ?";
}
if ($is_active_value) {
    $sql .= " AND is_active = ?";
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
if ($is_active_value) {
    $types .= 'i';
    $params[] = &$is_active_value;
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
    <?php include '../../components/business/header.php'; ?>
    <section class="container py px">
        <div class="admin-bar">
            <h1>Administrar Usuarios</h1>
            <a class="btn btn-primary" href="<?php echo BASE_URL ?>/business/usuarios/add.php">Añadir usuario</a>
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
                <label for="full_name">Nombre completo:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo $full_name_value_display; ?>">
            </div>
            <!-- filtrar por rol -->
            <div class="input-wrapper select-input">
                <label for="role">Seleccionar rol:</label>
                <select id="role" name="role">
                    <option value="" <?php echo ($role_value == '') ? 'selected' : ''; ?>>Selecciona...</option>
                    <option value="admin" <?php echo ($role_value == 'admin') ? 'selected' : ''; ?>>Administrador de Empresa</option>
                    <option value="analyst" <?php echo ($role_value == 'analyst') ? 'selected' : ''; ?>>Analista de Empresa</option>
                </select>
            </div>
            <!-- filtrar por estado -->
            <div class="input-wrapper select-input">
                <label for="is_active">Seleccionar estado:</label>
                <select id="is_active" name="is_active">
                    <option value="" <?php echo ($is_active_value == '') ? 'selected' : ''; ?>>Selecciona...</option>
                    <option value="1" <?php echo ($is_active_value == '1') ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo ($is_active_value == '0') ? 'selected' : ''; ?>>Inactivo</option>
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