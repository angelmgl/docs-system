<?php

require './config.php';

// Función para verificar si hay usuarios en la tabla.
function hasUsers($mydb) {
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = $mydb->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] > 0;
}

// Función para insertar el usuario admin en la base de datos.
function createAdmin($email, $password, $full_name, $username, $mydb) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $mydb->prepare("INSERT INTO users (email, password, full_name, username, role) VALUES (?, ?, ?, ?, 'super')");
    $stmt->bind_param("ssss", $email, $hashedPassword, $full_name, $username);
    
    if ($stmt->execute()) {
        echo "Usuario administrador creado con éxito.\n";
    } else {
        echo "Error al crear el usuario administrador: " . $mydb->error . "\n";
    }

    $stmt->close();
}

if (hasUsers($mydb)) {
    echo "Ya hay usuarios registrados en la base de datos.\n";
} else {
    // Capturar el input del usuario.
    echo "Crear un usuario administrador:\n";
    $email = readline("Introduce el email: ");
    $password = readline("Introduce la contraseña: ");
    $full_name = readline("Introduce el nombre completo: ");
    $username = readline("Introduce el nombre de usuario: ");

    // Crear el usuario admin en la base de datos.
    createAdmin($email, $password, $full_name, $username, $mydb);
}

$mydb->close();
