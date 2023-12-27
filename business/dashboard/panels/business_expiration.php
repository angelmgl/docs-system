<?php

$sql = "SELECT * FROM businesses WHERE id = $my_business";

$stmt = $mydb->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$business = null;

if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
}

$days = days_until_date($business['expiration_date']);

?>

<div class="panel" style="grid-column: 1 / span 2;">
    <h2><?php echo $business['name'] ?></h2>

    <?php if ($days > 10) { ?>
        <p class="success">Te quedan <?php echo $days ?> días de suscripción ¡Trabaja tranquilo!</p>
    <?php } elseif ($days > 7) { ?>
        <p class="warning">Atento, te quedan <?php echo $days ?> días de suscripción.</p>
    <?php } elseif ($days > 2) { ?>
        <p class="error">Solo te quedan <?php echo $days ?> días de suscripción, considera renovar tu suscripción para no perder acceso a tu negocio.</p>
    <?php } elseif ($days > 1) { ?>
        <p class="error"><strong>¡Mañana expira tu suscripción!</strong> Renová tu suscripción ahora para no perder acceso a tu negocio.</p>
    <?php } else { ?>
        <p class="error"><strong>¡CUIDADO, TU SUSCRIPCIÓN ACABA HOY!</strong> Renová tu suscripción ahora para no perder acceso a tu negocio.</p>
    <?php } ?>

    <p>
        Tu suscripción expira el <strong><?php echo format_date($business['expiration_date'], false) ?></strong>.
    </p>

    <?php if ($days < 7) { ?>
        <p>
            Una vez expira tu suscripción, perdés acceso a tu empresa, lo cual significa que ni vos ni tus analistas podrán iniciar sesión en <?php echo $business['name'] ?>. 
            ¡Pero no te preocupes! Tus datos están a salvo, renueva tu suscripción para recuperar el acceso de inmediato.
        </p>
        <br>
        <a class="btn btn-primary" href="<?php echo CONTACT_URL ?>" target="_blank">
            Contactános para renovar
        </a>
    <?php } ?>
</div>