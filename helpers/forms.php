<?php


// función para recuperar el valor por defecto de un input, cuando falla un form, a través de la key 
function get_form_data($key) {
    return isset($_SESSION['form_data'][$key]) ? $_SESSION['form_data'][$key] : '';
}

// función para establecer un mensaje de error y guardar los datos enviador de una solicitud fallida
function handle_form_error($error_message, $form_data, $return_url) {
    $_SESSION['error'] = $error_message;
    $_SESSION['form_data'] = $form_data;
    header("Location: " . BASE_URL . $return_url);
    exit;
}

// función para subir fotos al sistema y devolver la url
function upload_photo($file, $upload_system_dir, $upload_url_dir) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $file['tmp_name'];

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename_without_extension = pathinfo($file['name'], PATHINFO_FILENAME);

        // Limpiar el nombre del archivo: reemplaza espacios con guiones bajos y convierte a minúsculas
        $clean_name = str_replace(' ', '_', $filename_without_extension);
        $clean_name = strtolower($clean_name);

        $new_name = $clean_name . "_" . time() . "." . $file_extension;

        $final_system_path = $upload_system_dir . $new_name;
        $final_url_path = $upload_url_dir . $new_name;

        if (move_uploaded_file($tmp_name, $final_system_path)) {
            return $final_url_path;
        } else {
            throw new Exception("No se pudo subir la imagen.");
        }
    }

    return NULL;
}
