<?php

require '../../config/config.php';
require '../../helpers/business.php';
require '../../helpers/roles.php';

session_start();

$user_id = $_SESSION["user_id"];

verifyAuthentication($user_id);

$stmt = $mydb->prepare("
    SELECT b.*, r.name AS role_name, r.code AS role_code
    FROM businesses b
    INNER JOIN roles_businesses rb ON b.id = rb.business_id
    INNER JOIN roles r ON rb.role_id = r.id
    WHERE rb.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$businesses = [];
while ($row = $result->fetch_assoc()) {
    $businesses[] = $row;
}

$stmt->close();
$mydb->close();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Bienvenido</title>
    <?php include '../../components/meta.php' ?>
</head>

<body>
    <section class="container py px" id="welcome-page">
        <h1>Bienvenido <?php echo $_SESSION['full_name']; ?> a <?php echo APP_NAME; ?></h1>
        <p>Para continuar selecciona la empresa donde quieres continuar:</p>

        <div class="business-list">
            <?php
            if (empty($businesses)) { ?>
                <p style="text-align: center;">No tienes negocios asignados...</p>
                <form action="<?php echo BASE_URL ?>/actions/auth_logout.php" method="post">
                    <button style="display: flex; margin: 0 auto;" class="btn btn-danger" type="submit" name="logout">Cerrar sesión</button>
                </form>
                <?php } else {
                // Iterar sobre los negocios si hay alguno
                foreach ($businesses as $business) {
                ?>
                    <div class="business-card">
                        <div class="business-logo" style="background-image: url(<?php echo get_logo($business) ?>)"></div>
                        <div class="business-info">
                            <h2><?php echo $business['name']; ?></h2>
                            <p><?php echo $business['role_name']; ?></p>
                        </div>
                        <form method="POST" action="./actions/set_business.php">
                            <input type="hidden" name="business_id" value="<?php echo $business['id']; ?>" />
                            <input type="hidden" name="business_logo" value="<?php echo $business['logo']; ?>" />
                            <input type="hidden" name="role_code" value="<?php echo $business['role_code']; ?>" />
                            <input type="submit" class="btn btn-primary" value="Ingresar" />
                        </form>
                    </div>
            <?php }
            } ?>
        </div>
    </section>
</body>

</html>