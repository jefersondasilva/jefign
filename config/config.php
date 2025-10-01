<?php

$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'localhost:8000']);

if ($isLocal) {
    
    if ($_SERVER['SERVER_PORT'] == '8000') {
        // Servidor PHP embutido
        define('BASE_URL', 'http://localhost:8000');
    } else {
        // XAMPP/Apache local
        define('BASE_URL', 'http://localhost/jefign');
    }
} else {
    // Configurações para produção
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST']);
}


define('ADMIN_URL', BASE_URL . '/admin');
define('API_URL', BASE_URL . '/api');
define('ASSETS_URL', BASE_URL . '/imagens');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Configurações de upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configurações de sessão
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'JEFIGN_ADMIN_SESSION');


function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function admin_url($path = '') {
    return ADMIN_URL . '/' . ltrim($path, '/');
}

function api_url($path = '') {
    return API_URL . '/' . ltrim($path, '/');
}

function asset_url($path = '') {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

function upload_url($path = '') {
    return UPLOADS_URL . '/' . ltrim($path, '/');
}
?>