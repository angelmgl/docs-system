<?php

require '../../config/config.php';
require '../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

$username = $_GET["username"];

$user = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM users WHERE username = ? AND business_id = ?");
$stmt->bind_param("si", $username, $my_business);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si no se encontró al usuario, redirige a la página de lista de usuarios.
if ($user === null) {
    header("Location: " . BASE_URL . "/business/usuarios");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Eliminar usuario</title>
    <?php include '../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../components/business/header.php'; ?>
    <main class="container px py content" id="remove-user">
        <h1>¿Estás seguro de que quieres eliminar a <?php echo $user["full_name"] ?>?</h1>

        <p>
            ¡Atención! Eliminar un usuario es una acción irreversible. Si no estás completamente
            seguro, considera desactivarlo para que ya no pueda usar su cuenta temporalmente. Puedes
            <a href="<?php echo BASE_URL ?>/business/usuarios/edit.php?username=<?php echo $user["username"]; ?>" class="semibold text-primary">
                desactivarlo aquí.
            </a>
        </p>

        <div class="remove-actions">
            <form action="./actions/delete_user.php" method="POST">
                <input type="hidden" name="username" value="<?php echo $user["username"]; ?>">
                <button type="submit" class="btn btn-primary">Si, eliminar usuario</button>
            </form>
            <a href="<?php echo BASE_URL ?>/business/usuarios" class="btn btn-secondary">No, cancelar</a>
        </div>
    </main>
</body>

</html>