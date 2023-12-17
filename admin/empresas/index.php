<?php

require '../../config/config.php';
require '../../helpers/business.php';
require '../../helpers/auth.php';
require '../../helpers/dates.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// filtros
$name_value = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
$is_active_value = isset($_GET['is_active']) ? $_GET['is_active'] : '';

// Crear SQL dinámico
$sql = "SELECT * FROM businesses WHERE 1=1";

if ($name_value) {
    $sql .= " AND name LIKE ?";
    $name_value = "%$name_value%";
}
if ($is_active_value !== '') {
    $sql .= " AND is_active = ?";
}

$stmt = $mydb->prepare($sql);

$params = [];
$types = '';
if ($name_value) {
    $types .= 's';
    $params[] = &$name_value;
}
if ($is_active_value !== '') {
    $types .= 'i';
    $params[] = &$is_active_value;
}
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}

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

$name_value_display = str_replace('%', '', $name_value);

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

        <form method="GET" class="custom-form filters-container grid cols-3">
            <!-- buscador por nombre -->
            <div class="input-wrapper text-input">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" value="<?php echo $name_value_display; ?>">
            </div>
            <!-- filtrar por estado -->
            <div class="input-wrapper select-input">
                <label for="is_active">Seleccionar estado:</label>
                <select id="is_active" name="is_active">
                    <option value="">Selecciona...</option>
                    <option value="1" <?php echo $is_active_value === '1' ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $is_active_value === '0' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <!-- buscar -->
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>

        <?php if (empty($businesses)) { ?>
            <p>No hay resultados para esta búsqueda...</p>
        <?php } else {
            include '../../components/admin/business_table.php';
        } ?>
    </section>
</body>

</html>