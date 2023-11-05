<?php

require '../config/config.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Preparar la consulta para obtener el usuario de la base de datos.
$stmt = $mydb->prepare("SELECT id, password, is_superuser FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verificar si el usuario existe y, si es así, si la contraseña es correcta.
if ($user && password_verify($password, $user['password'])) {
    // La contraseña es correcta y el usuario existe.
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;

    // Si es superusuario, se establece el rol y se redirige al dashboard de superadmin.
    if ($user['is_superuser']) {
        $_SESSION['role'] = 'superadmin';
        header("Location: " . BASE_URL . "/admin/dashboard");
        exit;
    } else {
        // No es superusuario, buscamos su rol en la tabla de roles_businesses.
        $roleStmt = $mydb->prepare("
            SELECT r.name 
            FROM roles_businesses rb
            INNER JOIN roles r ON rb.role_id = r.id
            WHERE rb.user_id = ?
            LIMIT 1
        ");
        $roleStmt->bind_param("i", $user['id']);
        $roleStmt->execute();
        $roleResult = $roleStmt->get_result();
        $roleRow = $roleResult->fetch_assoc();

        // Si el usuario tiene un rol asignado, establecemos el rol en la sesión.
        if ($roleRow) {
            $_SESSION['role'] = $roleRow['name'];
            header("Location: " . BASE_URL . "/business/dashboard.php");
            exit;
        } else {
            // El usuario no tiene un rol asignado, manejar según sea necesario.
            $_SESSION['error'] = "El usuario no pertenece a ningún negocio.";
            header("Location: " . BASE_URL . "/login.php");
            exit;
        }
    }

    // Actualizar el campo last_login para el usuario que ha iniciado sesión.
    $updateStmt = $mydb->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->bind_param("i", $user['id']);
    $updateStmt->execute();
    $updateStmt->close();
} else {
    // No se encontró el usuario o la contraseña no es correcta.
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$stmt->close();
$mydb->close();
