<?php

require '../../config/config.php';
require '../../helpers/auth.php';
$title = "Eliminar empresa";

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['superadmin']);

$business_id = $_GET["id"];

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

// Si no se encontró al usuario, redirige a la página de lista de usuarios.
if ($business === null) {
    header("Location: " . BASE_URL . "/admin/empresas");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/admin/header.php'; ?>
    <main class="container px py" id="remove-business">
        <h1>¿Estás seguro de que quieres eliminar <?php echo $business["name"] ?>?</h1>

        <p>
            ¡Atención! Eliminar una empresa es una acción irreversible. Si no estás completamente
            seguro, considera desactivarla para que ya no sea accesible en lugar de eliminarla
            permanentemente. Puedes
            <a href="<?php echo BASE_URL ?>/admin/empresas/edit.php?id=<?php echo $business["id"]; ?>" class="semibold text-primary">
                desactivarla aquí.
            </a>
        </p>

        <div class="remove-actions">
            <form action="./actions/delete_business.php" method="POST">
                <input type="hidden" name="business_id" value="<?php echo $business["id"]; ?>">
                <button type="submit" class="btn btn-primary">Si, eliminar</button>
            </form>
            <a href="<?php echo BASE_URL ?>/admin/empresas" class="btn btn-secondary">No, cancelar</a>
        </div>
    </main>
</body>

</html>