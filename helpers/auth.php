<?php 

function verifyAuthentication($user_id) {
    if(!$user_id) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function verifyRoles($roles) {
    if (!in_array($_SESSION['role'], $roles)) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}