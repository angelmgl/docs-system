<?php 

function verifyRoles($roles) {
    if (!in_array($_SESSION['role'], $roles)) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}