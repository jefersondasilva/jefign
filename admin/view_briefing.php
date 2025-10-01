<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../backend/classes/AuthManager.php';
require_once __DIR__ . '/../backend/classes/BriefingManager.php';

$auth = new AuthManager();
$auth->requireAuth();

$briefingManager = new BriefingManager();
$briefingId = $_GET['id'] ?? 0;

if (!$briefingId) {
    header('Location: dashboard.php');
    exit;
}

$briefing = $briefingManager->getBriefingById($briefingId);

if (!$briefing) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Briefing #<?php echo $briefing['id']; ?> - JEFIGN Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-blue: #366BB3;
            --ink: #1E1E1E;
            --indigo: #5D57A1;
            --sky: #B5D1EF;
            --paper: #FAFAFA;
            --muted: #8EA5C4;
            --radius: 16px;
            --shadow-1: 0 10px 30px rgba(0,0,0,.12);
            --shadow-2: 0 20px 50px rgba(0,0,0,.18);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Inter, sans-serif;
            background: linear-gradient(180deg, #181A1F 0%, var(--ink) 100%);
            color: var(--paper);
            min-height: 100vh;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 0;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--brand-blue), var(--indigo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand-blue), var(--indigo));
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .briefing-header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .briefing-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 16px;
        }
        
        .briefing-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            color: var(--muted);
        }
        
        .briefing-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            padding: 30px;
        }
        
        .section h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--sky);
        }
        
        .field {
            margin-bottom: 20px;
        }
        
        .field-label {
            font-weight: 600;
            color: var(--sky);
            margin-bottom: 8px;
            display: block;
        }
        
        .field-value {
            color: var(--paper);
            line-height: 1.6;
        }
        
        .services-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .service-tag {
            background: rgba(54, 107, 179, 0.2);
            color: var(--brand-blue);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .image-preview {
            max-width: 300px;
            border-radius: 8px;
            box-shadow: var(--shadow-1);
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-novo {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-em_andamento {
            background: rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
        }
        
        .status-concluido {
            background: rgba(25, 135, 84, 0.2);
            color: #198754;
        }
        
        @media (max-width: 768px) {
            .briefing-meta {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 20px 10px;
            }
            
            .section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>JEFIGN Admin</h1>
            </div>
            <a href="dashboard.php" class="btn">← Voltar ao Dashboard</a>
        </div>
    </header>
    
    <div class="container">
        <div class="briefing-header">
            <h1 class="briefing-title">Briefing #<?php echo $briefing['id']; ?> - <?php echo htmlspecialchars($briefing['empresa']); ?></h1>
            <div class="briefing-meta">
                <div>
                    <strong>Data de Envio:</strong><br>
                    <?php echo date('d/m/Y H:i', strtotime($briefing['created_at'])); ?>
                </div>
                <div>
                    <strong>Status:</strong><br>
                    <span class="status-badge status-<?php echo $briefing['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $briefing['status'])); ?>
                    </span>
                </div>
                <div>
                    <strong>Email:</strong><br>
                    <a href="mailto:<?php echo htmlspecialchars($briefing['email']); ?>" style="color: var(--brand-blue);">
                        <?php echo htmlspecialchars($briefing['email']); ?>
                    </a>
                </div>
                <div>
                    <strong>Telefone:</strong><br>
                    <a href="tel:<?php echo htmlspecialchars($briefing['telefone']); ?>" style="color: var(--brand-blue);">
                        <?php echo htmlspecialchars($briefing['telefone']); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="briefing-content">
            <!-- Informações do Cliente -->
            <div class="section">
                <h3>1. Informações do Cliente</h3>
                <div class="field">
                    <span class="field-label">Nome da empresa / marca:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['empresa']); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Nome do responsável / contato:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['responsavel']); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Website / redes sociais:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['website'] ?: 'Não informado'); ?></div>
                </div>
            </div>
            
            <!-- Sobre a Marca -->
            <div class="section">
                <h3>2. Sobre a Marca</h3>
                <div class="field">
                    <span class="field-label">Segmento / área de atuação:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['segmento'] ?: 'Não informado'); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Tempo no mercado:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['tempo'] ?: 'Não informado'); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Valores da marca:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['valores'] ?: 'Não informado')); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Missão da marca:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['missao'] ?: 'Não informado')); ?></div>
                </div>
            </div>
            
            <!-- Objetivo do Projeto -->
            <div class="section">
                <h3>3. Objetivo do Projeto</h3>
                <div class="field">
                    <span class="field-label">Principal objetivo:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['objetivo'] ?: 'Não informado'); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">O que a marca deve transmitir:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['mensagem_marca'] ?: 'Não informado')); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Problemas ou desafios a resolver:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['desafios'] ?: 'Não informado')); ?></div>
                </div>
            </div>
            
            <!-- Público-Alvo -->
            <div class="section">
                <h3>4. Público-Alvo</h3>
                <div class="field">
                    <span class="field-label">Clientes ideais:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['clientes'] ?: 'Não informado')); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Idade, gênero, localização:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['idade'] ?: 'Não informado'); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Hábitos e interesses:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['habitos'] ?: 'Não informado')); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Linguagem / estilo desejado:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['linguagem'] ?: 'Não informado'); ?></div>
                </div>
            </div>
            
            <!-- Concorrência -->
            <div class="section">
                <h3>5. Concorrência</h3>
                <div class="field">
                    <span class="field-label">Principais concorrentes:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['concorrentes'] ?: 'Não informado')); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Preferências sobre concorrentes:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['preferencias'] ?: 'Não informado')); ?></div>
                </div>
            </div>
            
            <!-- Estilo e Inspirações -->
            <div class="section">
                <h3>6. Estilo e Inspirações</h3>
                <div class="field">
                    <span class="field-label">Paletas de cores:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['cores'] ?: 'Não informado'); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Tipos de fontes:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['fontes'] ?: 'Não informado'); ?></div>
                </div>
                <?php if ($briefing['imagem_referencia']): ?>
                <div class="field">
                    <span class="field-label">Imagem de referência:</span>
                    <div class="field-value">
                        <img src="../uploads/<?php echo htmlspecialchars($briefing['imagem_referencia']); ?>" 
                             alt="Imagem de referência" class="image-preview">
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Serviços Contratados -->
            <div class="section">
                <h3>7. Serviços Contratados</h3>
                <div class="field">
                    <span class="field-label">Serviços solicitados:</span>
                    <div class="field-value">
                        <?php if (!empty($briefing['servicos'])): ?>
                            <div class="services-list">
                                <?php foreach ($briefing['servicos'] as $servico): ?>
                                    <span class="service-tag"><?php echo htmlspecialchars($servico); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            Não informado
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Prazos e Entregáveis -->
            <div class="section">
                <h3>8. Prazos e Entregáveis</h3>
                <div class="field">
                    <span class="field-label">Data de início desejada:</span>
                    <div class="field-value"><?php echo $briefing['data_inicio'] ? date('d/m/Y', strtotime($briefing['data_inicio'])) : 'Não informado'; ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Data de entrega desejada:</span>
                    <div class="field-value"><?php echo $briefing['data_entrega'] ? date('d/m/Y', strtotime($briefing['data_entrega'])) : 'Não informado'; ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Etapas importantes:</span>
                    <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['etapas'] ?: 'Não informado')); ?></div>
                </div>
                <div class="field">
                    <span class="field-label">Formato dos arquivos:</span>
                    <div class="field-value"><?php echo htmlspecialchars($briefing['formato'] ?: 'Não informado'); ?></div>
                </div>
            </div>
            
            <!-- Observações -->
            <?php if ($briefing['observacoes']): ?>
            <div class="section">
                <h3>9. Observações Adicionais</h3>
                <div class="field-value"><?php echo nl2br(htmlspecialchars($briefing['observacoes'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>