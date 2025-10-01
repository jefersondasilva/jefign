<?php


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// Rota para área administrativa
if ($uri === '/admin' || $uri === '') {
    if ($uri === '/admin') {
        require_once __DIR__ . '/admin/index.php';
        return;
    }
}

// Rotas admin específicas
if (preg_match('/^\/admin\/(.+)$/', $uri, $matches)) {
    $file = $matches[1];
    $adminFile = __DIR__ . '/admin/' . $file;
    
    if (file_exists($adminFile)) {
        require_once $adminFile;
        return;
    }
}

// Rota para API
if (preg_match('/^\/api\/(.+)$/', $uri, $matches)) {
    $file = $matches[1];
    $apiFile = __DIR__ . '/backend/api/' . $file;
    
    if (file_exists($apiFile)) {
        require_once $apiFile;
        return;
    }
}

// Rota para briefing
if ($uri === '/briefing') {
    require_once __DIR__ . '/briefing.html';
    return;
}

// Rota para envio de briefing
if ($uri === '/submit-briefing') {
    require_once __DIR__ . '/backend/api/submit_briefing.php';
    return;
}


if (strpos($uri, '/backend') === 0 || strpos($uri, '/config') === 0) {
    http_response_code(403);
    echo 'Acesso negado';
    return;
}


$file = __DIR__ . $uri;
if (file_exists($file) && is_file($file)) {
    return false; 
}


if ($uri === '' || $uri === '/') {
    require_once __DIR__ . '/index.html';
    return;
}


http_response_code(404);
echo 'Página não encontrada';
?>