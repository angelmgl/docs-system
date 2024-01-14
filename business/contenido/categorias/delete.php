<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['admin']);

$my_business = $_SESSION['business_id'];

$category_id = $_GET["category_id"];

$category = null;

// preparar la consulta
$stmt = $mydb->prepare("SELECT * FROM categories WHERE id = ? AND business_id = ?");
$stmt->bind_param("ii", $category_id, $my_business);

// ejecutar la consulta
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $category = $result->fetch_assoc();
}

$stmt->close();
$mydb->close();

// Si no se encontró a la categoría, redirige a la página de lista de categorías.
if ($category === null) {
    header("Location: " . BASE_URL . "/business/contenido");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include '../../../components/meta.php'; ?>
</head>

<body>
    <?php include '../../../components/business/header.php'; ?>
    <main class="container px py content" id="remove-user">
        <h1>¿Estás seguro de que quieres eliminar la categoría <?php echo $category["name"] ?>?</h1>

        <p>
            ¡Atención! Eliminar una categoría es una acción irreversible. La categoría y todos sus documentos asociados se 
            eliminarán irreversiblemente.
        </p>

        <div class="remove-actions">
            <form action="./actions/delete_category.php" method="POST">
                <input type="hidden" name="category_id" value="<?php echo $category["id"]; ?>">

                <button type="submit" class="btn btn-primary">Si, eliminar categoría</button>
            </form>
            <a href="<?php echo BASE_URL ?>/business/contenido" class="btn btn-secondary">No, cancelar</a>
        </div>
    </main>
</body>

</html>