<?php 

require '../../../config/config.php';
require '../../../helpers/auth.php';

session_start();

$user_id = $_SESSION["user_id"];

verifyAuthentication($user_id);

$business_id = $_POST['business_id'];
$business_logo = $_POST['business_logo'];
$role_code = $_POST['role_code'];

// Asignar roles guardando en la sesión
$_SESSION['role'] = $role_code;
$_SESSION['logo'] = $business_logo;
$_SESSION['current_business'] = $business_id;

header("Location: " . BASE_URL . "/business/dashboard");
exit;