<?php

require '../config/config.php';
require '../helpers/users.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Preparar la consulta para obtener el usuario de la base de datos.
$stmt = $mydb->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

function set_session($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['business_id'] = $user['business_id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['profile_picture'] = $user['profile_picture'];
}

// Verificar si el usuario existe y, si es así, si la contraseña es correcta.
if ($user && password_verify($password, $user['password'])) {

    if ($user['role'] === 'super') {
        // si el usuario es superadmin redirigimos al admin de la app
        set_session($user);
        header("Location: " . BASE_URL . "/admin/dashboard");
        exit;
    } else if (in_array($user['role'], ['admin', 'analyst']) && !!$user['business_id']) {
        // Si es admin o analyst, verifica el estado del negocio.
        $business_stmt = $mydb->prepare("SELECT is_active FROM businesses WHERE id = ?");
        $business_stmt->bind_param("i", $user['business_id']);
        $business_stmt->execute();
        $business_result = $business_stmt->get_result();
        $business = $business_result->fetch_assoc();

        if ($business && $business['is_active']) {
            // El negocio está activo. Continuar con el inicio de sesión.
            set_session($user);
            header("Location: " . BASE_URL . "/business/dashboard");
            exit;
        } else {
            // Negocio inactivo. Redirigir a la página de expiración.
            $_SESSION['this_role'] = $user['role'];
            header("Location: " . BASE_URL . "/expired.php");
            exit;
        }
    } else {
        // El negocio no está asignado o el rol del usuario no es válido.
        $_SESSION['error'] = "El usuario no tiene un negocio asignado o el rol no es válido.";
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }

    update_last_login($mydb, $user['id']);

    $stmt->close();
    $mydb->close();

    exit();
} else {
    $stmt->close();
    $mydb->close();

    // No se encontró el usuario o la contraseña no es correcta.
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: " . BASE_URL . "/login.php");
    exit();
}
