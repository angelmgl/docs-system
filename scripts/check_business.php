<?php

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../helpers/notifications.php';
require __DIR__ . '/../helpers/dates.php';

// Selecciona las empresas expiradas y obtén los administradores en un solo query
$sql = "
    SELECT businesses.*, users.full_name AS admin_full_name, users.email AS admin_email
    FROM businesses
    LEFT JOIN users ON businesses.id = users.business_id
    WHERE businesses.is_active = 1 
    AND businesses.expiration_date < CURDATE()
    AND users.role = 'admin'";
$result = $mydb->query($sql);

if ($result->num_rows > 0) {
    // Itera sobre las empresas expiradas y administra la desactivación y notificación
    while ($row = $result->fetch_assoc()) {
        $date = format_date($row['expiration_date'], false);
        expired_notification(
            $row['admin_full_name'],
            $row['admin_email'],
            $row['name'],
            $date
        );

        echo "----";

        // Desactiva la empresa
        $update_sql = "
            UPDATE businesses 
            SET is_active = 0
            WHERE id = " . $row['id'];
        $update_result = $mydb->query($update_sql);
    }

    echo "Se desactivaron " . $result->num_rows . " empresas y se notificaron a los administradores.";
} else {
    echo "No hay empresas que hayan expirado.";
}

?>
