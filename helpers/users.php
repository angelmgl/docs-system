<?php

function get_profile_picture($user)
{
    if (isset($user["profile_picture"]) && $user["profile_picture"]) {
        return BASE_URL . $user["profile_picture"];
    } else {
        return BASE_URL . '/assets/img/avatar.webp';
    }
}

function get_last_login($user)
{
    $date = $user["last_login"];
    $is_active = $user["is_active"] == 1;

    if ($date && $is_active) {
        return 'Última vez el ' . format_date($date);
    } else if ($is_active) {
        return 'Aún no se ha conectado...';
    } else {
        return '🚫 Usuario inactivo.';
    }
}

function update_last_login($mydb, $id)
{
    // Actualizar el campo last_login para el usuario que ha iniciado sesión.
    $updateStmt = $mydb->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->bind_param("i", $id);
    $updateStmt->execute();
    $updateStmt->close();
}

function get_role($user) 
{
    $role = $user["role"];

    switch ($role) {
        case 'super':
            return "Super Administrador";
        case 'admin':
            return "Administrador";
        case 'analyst':
            return "Analista";
    }
}

function generate_reset_code($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}