<?php

ob_start();


error_reporting(E_ERROR | E_PARSE);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../classes/BriefingManager.php';

// Função para enviar JSON limpo
function sendJsonResponse($data, $httpCode = 200) {
    // Limpar qualquer output anterior
    if (ob_get_level()) {
        ob_clean();
    }
    
    http_response_code($httpCode);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
}

try {
    $briefingManager = new BriefingManager();
    
    // Capturar dados do formulário
    $data = $_POST;
    
    
    $imageName = null;
    if (isset($data['imagem_referencia_data']) && !empty($data['imagem_referencia_data'])) {
        $imageData = $data['imagem_referencia_data'];
        $imageFileName = $data['imagem_referencia_name'] ?? 'referencia.jpg';
        $imageType = $data['imagem_referencia_type'] ?? 'image/jpeg';
        
        $imageName = $briefingManager->processImageUpload($imageData, $imageFileName, $imageType);
        
        if (!$imageName) {
            sendJsonResponse(['success' => false, 'message' => 'Erro no upload da imagem']);
        }
    }
    

    $data['imagem_referencia'] = $imageName;
    
  
    $requiredFields = ['empresa', 'responsavel', 'email', 'telefone'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(['success' => false, 'message' => "Campo obrigatório: $field"]);
        }
    }
    
    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(['success' => false, 'message' => 'Email inválido']);
    }
    
    // Salvar briefing no banco de dados
    $briefingId = $briefingManager->saveBriefing($data);
    
    if ($briefingId) {
       
        $emailSent = sendNotificationEmail($data);
        
        sendJsonResponse([
            'success' => true, 
            'message' => 'Briefing enviado com sucesso!',
           
        ]);
    } else {
        sendJsonResponse(['success' => false, 'message' => 'Erro ao salvar briefing']);
    }
    
} catch (Exception $e) {
    error_log("Erro no submit_briefing.php: " . $e->getMessage());
    sendJsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
}


function sendNotificationEmail($data) {
  
    
    $to = 'contato@jefign.com'; 
    $subject = 'Novo Briefing Recebido - ' . $data['empresa'];
    $message = "
    Novo briefing recebido:
    
    Empresa: {$data['empresa']}
    Responsável: {$data['responsavel']}
    Email: {$data['email']}
    Telefone: {$data['telefone']}
    
    Acesse o painel administrativo para ver todos os detalhes.
    ";
    
    $headers = "From: noreply@jefign.com\r\n";
    $headers .= "Reply-To: {$data['email']}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
  
     return mail($to, $subject, $message, $headers);
    
    return true; 
}
?>