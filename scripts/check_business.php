<?php 

require '../config/config.php';
require '../helpers/notifications.php';

// Selecciona las empresas que vencieron antes de hoy
$sql = "
    SELECT *
    FROM businesses 
    WHERE is_active = 1 
    AND expiration_date < CURDATE()";
$result = $mydb->query($sql);

// Si hay propiedades que vencieron
if ($result->num_rows > 0) {
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