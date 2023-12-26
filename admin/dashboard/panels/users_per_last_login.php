<?php

$sql = "SELECT 
        u.*, 
        b.name AS business_name
    FROM 
        users u
    LEFT JOIN 
        businesses b ON u.business_id = b.id
    ORDER BY 
        u.last_login DESC
    LIMIT 20";

$stmt = $mydb->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$latest_users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $latest_users[] = $row;
    }
}


?>

<div class="panel" style="grid-column: 1 / span 2;">
    <h2>Usuarios por última conexión</h2>

    <div>
        <?php foreach ($latest_users as $user) { ?>
            <p>
                <a class="link" href="<?php echo BASE_URL . "/admin/usuarios/edit.php?username=" . $user['username'] ?>">
                    <?php echo $user['full_name'] ?>
                </a>
                &nbsp;(<?php echo $user['business_name'] ? $user['business_name'] : 'Super Administrador' ?>)
                - <?php echo $user['last_login'] ? 'última vez el ' . format_date($user['last_login']) : "aún no se ha conectado..." ?>
            </p>
        <?php } ?>
    </div>
</div>