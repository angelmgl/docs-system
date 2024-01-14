<?php

require  __DIR__ . '/../config/config.php';
require  __DIR__ . '/../helpers/notifications.php';
require  __DIR__ . '/../helpers/dates.php';

// Selecciona las empresas por expirar y obtén los administradores en un solo query
$sql = "
    SELECT businesses.*, users.full_name AS admin_full_name, users.email AS admin_email
    FROM businesses
    LEFT JOIN users ON businesses.id = users.business_id
    WHERE businesses.is_active = 1 
    AND businesses.expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
    AND users.role = 'admin'";
$result = $mydb->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $full_name = $row["admin_full_name"];
        $email = $row["admin_email"];
        $business_name = $row["name"]; 
        $days_to_expire = days_until_date($row["expiration_date"]); 
        $expiration_date = format_date($row["expiration_date"], false);

        expiring_soon_notification($full_name, $email, $business_name, $days_to_expire, $expiration_date);
    }
} else {
    echo "No hay empresas próximas a vencer en los próximos 5 días.";
}

?>