<?php

$sql = "SELECT * FROM businesses WHERE expiration_date < CURDATE()";
$stmt = $mydb->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$businesses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

?>

<div class="panel">
    <h2>Empresas cuya suscripción ya terminó</h2>

    <div>
        <?php foreach ($businesses as $business) { ?>
            <div class="my-business-card" style="margin-bottom: 10px;">
                <div class="logo" style="background-image: url(<?php echo get_logo($business) ?>)"></div>
                <div class="my-business-content">
                    <p class="my-business-name"><?php echo $business["name"] ?></p>
                    <p style="margin: 8px 0;" class="my-business-role">
                        <span style="margin-right: 10px; font-size: 14px;" class="status <?php echo $business['is_active'] === 1 ? 'active' : 'inactive' ?>">
                            <?php echo $business['is_active'] === 1 ? 'Activo' : 'Inactivo' ?>
                        </span>Expiró el <?php echo format_date($business['expiration_date'], false) ?>
                    </p>
                    <a class="link" href="<?php echo BASE_URL . "/admin/empresas/edit.php?id=" . $business['id'] ?>">Ver detalles.</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>