<?php

require '../../../config/config.php';
require '../../../helpers/forms.php';
require '../../../helpers/auth.php';
require '../../../helpers/users.php';
require '../../../helpers/notifications.php';

// iniciar sesión y verificar autorización
session_start();

verifyRoles(['super']);

// Recibe los datos del formulario.
if (isset($_FILES['csv_file'])) {
    $csv_file = $_FILES['csv_file']['tmp_name'];
    $filename = $_FILES['csv_file']['name'];
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $business_id = $_POST['business_id'];

    // Valores seteados por default
    $role = "analyst";
    $is_active = 1;

    // Validar que el archivo enviado es un CSV
    if ($file_extension != 'csv') {
        handle_form_error("Error: Archivo no válido", array(), "/admin/usuarios/block.php");
    } else {
        $users_created_count = 0;

        // Abre y lee el archivo CSV
        if (($handle = fopen($csv_file, "r")) !== FALSE) {
            $first_row = true; // primera fila de títulos

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($first_row) {
                    $first_row = false;
                    continue; 
                }

                $username = $data[0];
                $full_name = $data[1];
                $email = $data[2];
                $random = generate_reset_code();
                $password = password_hash($random, PASSWORD_DEFAULT); 

                user_created_notification($random, $email, $full_name, $username);

                // Insertar usuario en la base de datos
                $stmt = $mydb->prepare("INSERT INTO users (username, full_name, email, password, role, business_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssii", $username, $full_name, $email, $password, $role, $business_id, $is_active);

                try {
                    $stmt->execute();
                } catch (mysqli_sql_exception $e) {
                    $stmt->close();
                    fclose($handle);
                    $mydb->close();
                    handle_form_error("Error al crear el usuario: " . $e->getMessage(), array(), "/admin/usuarios/block.php");
                    exit; 
                }
                $users_created_count++;

                $stmt->close();
            }
            fclose($handle);
        }

        $mydb->close();

        $_SESSION['success'] = "$users_created_count usuarios creados exitosamente";
        header("Location: " . BASE_URL . "/admin/usuarios");
        exit;
    }
} else {
    handle_form_error("Error: No se recibió ningún archivo", array(), "/admin/usuarios/block.php");
}
