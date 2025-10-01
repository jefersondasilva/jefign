<?php
/**
 * Script CLI para criar usuários administrativos
 * Uso: php create_admin.php
 */


if (php_sapi_name() !== 'cli') {
    die("Este script deve ser executado via linha de comando.\n");
}

require_once __DIR__ . '/backend/classes/AuthManager.php';

echo "=== CRIAÇÃO DE USUÁRIO ADMINISTRATIVO - JEFIGN ===\n\n";


function readInput($prompt, $hidden = false) {
    echo $prompt;
    
    if ($hidden) {
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            
            $input = shell_exec('powershell -Command "$Password = Read-Host -AsSecureString; [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($Password))"');
        } else {
            
            system('stty -echo');
            $input = trim(fgets(STDIN));
            system('stty echo');
            echo "\n";
        }
    } else {
        $input = trim(fgets(STDIN));
    }
    
    return $input;
}


function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


function isValidPassword($password) {
    return strlen($password) >= 8;
}

try {
    $auth = new AuthManager();
    
   
    echo "Por favor, forneça os dados do novo administrador:\n\n";
    
   
    do {
        $username = readInput("Nome de usuário: ");
        if (empty($username)) {
            echo "Nome de usuário não pode estar vazio.\n";
        } elseif (strlen($username) < 3) {
            echo "Nome de usuário deve ter pelo menos 3 caracteres.\n";
        }
    } while (empty($username) || strlen($username) < 3);
    
  
    do {
        $email = readInput("Email: ");
        if (empty($email)) {
            echo "Email não pode estar vazio.\n";
        } elseif (!isValidEmail($email)) {
            echo "Email inválido.\n";
        }
    } while (empty($email) || !isValidEmail($email));
    
    
    do {
        $password = readInput("Senha (mínimo 8 caracteres): ", true);
        if (empty($password)) {
            echo "Senha não pode estar vazia.\n";
        } elseif (!isValidPassword($password)) {
            echo "Senha deve ter pelo menos 8 caracteres.\n";
        }
    } while (empty($password) || !isValidPassword($password));
    

    do {
        $confirmPassword = readInput("Confirme a senha: ", true);
        if ($password !== $confirmPassword) {
            echo "Senhas não coincidem. Tente novamente.\n";
        }
    } while ($password !== $confirmPassword);
    
   
    echo "\n=== CONFIRMAÇÃO ===\n";
    echo "Nome de usuário: $username\n";
    echo "Email: $email\n";
    $confirm = readInput("\nDeseja criar este usuário? (s/N): ");
    
    if (strtolower($confirm) !== 's' && strtolower($confirm) !== 'sim') {
        echo "Operação cancelada.\n";
        exit(0);
    }
    
  
    echo "\nCriando usuário...\n";
    $result = $auth->createUser($username, $email, $password);
    
    if ($result['success']) {
        echo "\n✅ SUCESSO!\n";
        echo "Usuário administrativo criado com sucesso!\n\n";
        echo "Dados de acesso:\n";
        echo "URL: http://localhost/jefign/admin/login.php\n";
        echo "Usuário: $username\n";
        echo "Email: $email\n\n";
        echo "⚠️  IMPORTANTE: Guarde essas informações em local seguro!\n";
    } else {
        echo "\n❌ ERRO!\n";
        echo "Falha ao criar usuário: " . $result['message'] . "\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "\n❌ ERRO FATAL!\n";
    echo "Erro: " . $e->getMessage() . "\n";
    echo "\nVerifique se:\n";
    echo "1. O banco de dados MySQL está rodando\n";
    echo "2. As configurações em config/database.php estão corretas\n";
    echo "3. O banco de dados 'jefign_briefing' existe\n";
    exit(1);
}

echo "\n=== SCRIPT FINALIZADO ===\n";
?>