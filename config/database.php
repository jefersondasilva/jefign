<?php

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'jeferson');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_CHARSET', 'utf8mb4');


define('JWT_SECRET', 'sua_chave_secreta_muito_forte_aqui_' . md5(__DIR__));


define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB


date_default_timezone_set('America/Sao_Paulo');

/**
 * Classe para conexão com banco de dados
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}


function createTables() {
    $db = Database::getInstance()->getConnection();
    
    // Tabela de usuários administrativos
    $sql_users = "
    CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT TRUE
    )";
    
    // Tabela de briefings
    $sql_briefings = "
    CREATE TABLE IF NOT EXISTS briefings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        empresa VARCHAR(255) NOT NULL,
        responsavel VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        telefone VARCHAR(50) NOT NULL,
        website VARCHAR(255),
        segmento VARCHAR(255),
        tempo VARCHAR(100),
        valores TEXT,
        missao TEXT,
        objetivo VARCHAR(255),
        mensagem_marca TEXT,
        desafios TEXT,
        clientes TEXT,
        idade VARCHAR(100),
        habitos TEXT,
        linguagem VARCHAR(255),
        concorrentes TEXT,
        preferencias TEXT,
        cores VARCHAR(255),
        fontes VARCHAR(255),
        servicos JSON,
        data_inicio DATE,
        data_entrega DATE,
        etapas TEXT,
        formato VARCHAR(255),
        observacoes TEXT,
        imagem_referencia VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('novo', 'em_andamento', 'concluido') DEFAULT 'novo'
    )";
    
    try {
        $db->exec($sql_users);
        $db->exec($sql_briefings);
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao criar tabelas: " . $e->getMessage());
        return false;
    }
}


createTables();
?>