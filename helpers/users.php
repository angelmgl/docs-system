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
