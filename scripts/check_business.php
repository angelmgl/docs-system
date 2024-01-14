<?php 

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../helpers/notifications.php';
require __DIR__ . '/../helpers/dates.php';

// Selecciona las empresas que vencieron antes de hoy
$sql = "
    SELECT *
    FROM businesses 
    WHERE is_active = 1 
    AND expiration_date < CURDATE()";
$result = $mydb->query($sql);

// Si hay empresas que vencieron
if ($result->num_rows > 0) {
    // Obtener la lista de administradores de las empresas expiradas
    $admins_sql = "
        SELECT users.full_name, users.email, businesses.name AS business_name, businesses.expiration_date
        FROM users
        JOIN businesses ON users.business_id = businesses.id
        WHERE users.role = 'admin' 
        AND businesses.is_active = 1 
        AND businesses.expiration_date < CURDATE()";

    $admins_result = $mydb->query($admins_sql);

    if ($admins_result->num_rows > 0) {
        while ($admin_row = $admins_result->fetch_assoc()) {
            // Llamar a la función expired_notification para enviar un correo a cada administrador
            $date = format_date($admin_row['expiration_date'], false);
            expired_notification(
                $admin_row['full_name'],
                $admin_row['email'],
                $admin_row['business_name'],
                $date
            );
            echo "----";
        }
    }

    // Actualizar el estado de las propiedades que vencieron a "borrador"
    $update_sql = "
        UPDATE businesses 
        SET is_active = 0
        WHERE is_active = 1 
        AND expiration_date < CURDATE()";
    $update_result = $mydb->query($update_sql);

    if ($update_result) {
        echo "Se desactivaron " . $mydb->affected_rows . " empresas.";
    } else {
        echo "Hubo un error al actualizar las empresas: " . $mydb->error;
    }
} else {
    echo "No hay empresas que hayan expirado.";
}

?>