<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../backend/classes/AuthManager.php';

$auth = new AuthManager();
$auth->logout();

header('Location: login.php');
exit;
?>