<?php
require_once __DIR__ . '/../../config/database.php';


class BriefingManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    
    public function saveBriefing($data) {
        try {
            
            $servicos = [];
            if (isset($data['servicos'])) {
                if (is_array($data['servicos'])) {
                    $servicos = $data['servicos'];
                } else {
                    $servicos = [$data['servicos']];
                }
            }
            
            $sql = "INSERT INTO briefings (
                empresa, responsavel, email, telefone, website, segmento, tempo,
                valores, missao, objetivo, mensagem_marca, desafios, clientes,
                idade, habitos, linguagem, concorrentes, preferencias, cores,
                fontes, servicos, data_inicio, data_entrega, etapas, formato,
                observacoes, imagem_referencia
            ) VALUES (
                :empresa, :responsavel, :email, :telefone, :website, :segmento, :tempo,
                :valores, :missao, :objetivo, :mensagem_marca, :desafios, :clientes,
                :idade, :habitos, :linguagem, :concorrentes, :preferencias, :cores,
                :fontes, :servicos, :data_inicio, :data_entrega, :etapas, :formato,
                :observacoes, :imagem_referencia
            )";
            
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                ':empresa' => $data['empresa'] ?? '',
                ':responsavel' => $data['responsavel'] ?? '',
                ':email' => $data['email'] ?? '',
                ':telefone' => $data['telefone'] ?? '',
                ':website' => $data['website'] ?? '',
                ':segmento' => $data['segmento'] ?? '',
                ':tempo' => $data['tempo'] ?? '',
                ':valores' => $data['valores'] ?? '',
                ':missao' => $data['missao'] ?? '',
                ':objetivo' => $data['objetivo'] ?? '',
                ':mensagem_marca' => $data['mensagem_marca'] ?? '',
                ':desafios' => $data['desafios'] ?? '',
                ':clientes' => $data['clientes'] ?? '',
                ':idade' => $data['idade'] ?? '',
                ':habitos' => $data['habitos'] ?? '',
                ':linguagem' => $data['linguagem'] ?? '',
                ':concorrentes' => $data['concorrentes'] ?? '',
                ':preferencias' => $data['preferencias'] ?? '',
                ':cores' => $data['cores'] ?? '',
                ':fontes' => $data['fontes'] ?? '',
                ':servicos' => json_encode($servicos),
                ':data_inicio' => $data['data_inicio'] ?? null,
                ':data_entrega' => $data['data_entrega'] ?? null,
                ':etapas' => $data['etapas'] ?? '',
                ':formato' => $data['formato'] ?? '',
                ':observacoes' => $data['observacoes'] ?? '',
                ':imagem_referencia' => $data['imagem_referencia'] ?? null
            ]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Erro ao salvar briefing: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function getAllBriefings($limit = 50, $offset = 0) {
        try {
            $sql = "SELECT * FROM briefings ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $briefings = $stmt->fetchAll();
            
            
            foreach ($briefings as &$briefing) {
                $briefing['servicos'] = json_decode($briefing['servicos'], true) ?? [];
            }
            
            return $briefings;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar briefings: " . $e->getMessage());
            return [];
        }
    }
    
  
    public function getBriefingById($id) {
        try {
            $sql = "SELECT * FROM briefings WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $briefing = $stmt->fetch();
            
            if ($briefing) {
                $briefing['servicos'] = json_decode($briefing['servicos'], true) ?? [];
            }
            
            return $briefing;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar briefing: " . $e->getMessage());
            return false;
        }
    }
    
   
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE briefings SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':status' => $status, ':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Erro ao atualizar status: " . $e->getMessage());
            return false;
        }
    }
    
   
    public function getTotalBriefings() {
        try {
            $sql = "SELECT COUNT(*) as total FROM briefings";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            return $result['total'];
            
        } catch (PDOException $e) {
            error_log("Erro ao contar briefings: " . $e->getMessage());
            return 0;
        }
    }
    
   
    public function processImageUpload($imageData, $imageName, $imageType) {
        try {
            // Validar tipo de arquivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($imageType, $allowedTypes)) {
                return false;
            }
            
            // Gerar nome único para o arquivo
            $extension = pathinfo($imageName, PATHINFO_EXTENSION);
            $fileName = uniqid('ref_') . '.' . $extension;
            $filePath = UPLOAD_DIR . $fileName;
            
            // Criar diretório se não existir
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            
            // Salvar arquivo
            if (file_put_contents($filePath, base64_decode($imageData))) {
                return $fileName;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Erro no upload de imagem: " . $e->getMessage());
            return false;
        }
    }
}
?>