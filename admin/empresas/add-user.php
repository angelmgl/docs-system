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
$mydb->close();

// Si no se encontró a la empresa, redirige a la página de lista de empresas.
if ($business === null) {
    $mydb->close();
    header("Location: " . BASE_URL . "/admin/empresas");
    exit;
}
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
            <p>
                Para agregar un usuario a una empresa, debes ir a la sección de <a href="<?php echo BASE_URL ?>/admin/usuarios">Usuarios</a>,
                usa los filtros para encontrar al usuario deseado y editalo para darle un rol en esta empresa.
            </p>
            <p>
                Si el usuario no existe, primero debes crear un nuevo usuario y darle un rol en esta empresa.
            </p>
            <div style="margin-top: 40px;">
                <a href="<?php echo BASE_URL ?>/admin/usuarios" class="btn btn-primary">Buscar usuario</a>
                <a href="<?php echo BASE_URL ?>/admin/usuarios/add.php" class="btn btn-secondary">Crear nuevo</a>
            </div>
        </section>
    </main>
    <script src="<?php echo BASE_URL ?>/assets/js/users.js"></script>
</body>

</html>