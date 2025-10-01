<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../backend/classes/AuthManager.php';

$auth = new AuthManager();

if ($auth->isAuthenticated()) {
    
    header('Location: ' . admin_url('dashboard.php'));
    exit;
} else {
    
    header('Location: ' . admin_url('login.php'));
    exit;
}
?>