<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * Classe para gerenciar autenticação
 */
class AuthManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
   
        if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start([
                'cookie_lifetime' => 3600, // 1 hora
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict'
            ]);
        }
    }
    
    /**
     * Cria um novo usuário administrativo
     */
    public function createUser($username, $email, $password) {
        try {
            
            $sql = "SELECT id FROM admin_users WHERE username = :username OR email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username, ':email' => $email]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Usuário ou email já existe'];
            }
            
           
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
           
            $sql = "INSERT INTO admin_users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $passwordHash
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Usuário criado com sucesso'];
            }
            
            return ['success' => false, 'message' => 'Erro ao criar usuário'];
            
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do servidor'];
        }
    }
    
  
    public function login($username, $password) {
        try {
            $sql = "SELECT id, username, email, password_hash, is_active FROM admin_users WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
            
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Usuário não encontrado'];
            }
            
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Usuário inativo'];
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Senha incorreta'];
            }
            
            
            $this->updateLastLogin($user['id']);
            
           
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['login_time'] = time();
            
            return ['success' => true, 'message' => 'Login realizado com sucesso'];
            
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do servidor'];
        }
    }
    

    public function isAuthenticated() {
        if (!isset($_SESSION['admin_user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        
        if (time() - $_SESSION['login_time'] > 3600) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
  
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        return [
            'id' => $_SESSION['admin_user_id'],
            'username' => $_SESSION['admin_username'],
            'email' => $_SESSION['admin_email']
        ];
    }
    
   
    private function updateLastLogin($userId) {
        try {
            $sql = "UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar último login: " . $e->getMessage());
        }
    }
    
  
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit;
        }
    }
    
  
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
 
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>