<?php

$sql = "SELECT * FROM users WHERE business_id = $my_business ORDER BY last_login DESC";

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

<div class="panel">
    <h2>Usuarios por última conexión</h2>

    <div>
        <?php foreach ($latest_users as $user) { ?>
            <p>
                <a class="link" href="<?php echo BASE_URL . "/business/usuarios/edit.php?username=" . $user['username'] ?>">
                    <?php echo $user['full_name'] ?>
                </a>
                &nbsp;(<?php echo get_role($user) ?>)
                - <?php echo $user['last_login'] ? 'última vez el ' . format_date($user['last_login']) : "aún no se ha conectado..." ?>
            </p>
        <?php } ?>
    </div>
</div>