<?php 

function verifyRole($role) {
    if ($_SESSION['role'] !== $role) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}