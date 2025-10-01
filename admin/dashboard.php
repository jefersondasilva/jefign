<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../backend/classes/AuthManager.php';
require_once __DIR__ . '/../backend/classes/BriefingManager.php';

$auth = new AuthManager();
$auth->requireAuth(); 

$briefingManager = new BriefingManager();
$currentUser = $auth->getCurrentUser();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $briefingId = $_POST['briefing_id'] ?? 0;
        $newStatus = $_POST['status'] ?? '';
        
        if ($briefingManager->updateStatus($briefingId, $newStatus)) {
            $message = 'Status atualizado com sucesso!';
        } else {
            $message = 'Erro ao atualizar status.';
        }
    }
}


$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$briefings = $briefingManager->getAllBriefings($limit, $offset);
$totalBriefings = $briefingManager->getTotalBriefings();
$totalPages = ceil($totalBriefings / $limit);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JEFIGN Admin</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
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
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-1);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            padding: 24px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--brand-blue);
            margin-bottom: 8px;
        }
        
        .briefings-table {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            overflow: hidden;
        }
        
        .table-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: 600;
            color: var(--sky);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-small {
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 6px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        
        .pagination a {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--paper);
            text-decoration: none;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .pagination a.active {
            background: var(--brand-blue);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #51cf66;
        }
        
        @media (max-width: 768px) {
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 12px 8px;
            }
            
            .actions {
                flex-direction: column;
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
            <div class="user-info">
                <span>Olá, <?php echo htmlspecialchars($currentUser['username']); ?></span>
                <a href="logout.php" class="btn btn-secondary">Sair</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalBriefings; ?></div>
                <div>Total de Briefings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($briefings, fn($b) => $b['status'] === 'novo')); ?></div>
                <div>Novos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($briefings, fn($b) => $b['status'] === 'em_andamento')); ?></div>
                <div>Em Andamento</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($briefings, fn($b) => $b['status'] === 'concluido')); ?></div>
                <div>Concluídos</div>
            </div>
        </div>
        
        <div class="briefings-table">
            <div class="table-header">
                <h2>Briefings Recebidos</h2>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empresa</th>
                        <th>Responsável</th>
                        <th>Email</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($briefings as $briefing): ?>
                    <tr>
                        <td>#<?php echo $briefing['id']; ?></td>
                        <td><?php echo htmlspecialchars($briefing['empresa']); ?></td>
                        <td><?php echo htmlspecialchars($briefing['responsavel']); ?></td>
                        <td><?php echo htmlspecialchars($briefing['email']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($briefing['created_at'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $briefing['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $briefing['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="view_briefing.php?id=<?php echo $briefing['id']; ?>" class="btn btn-small">Ver</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="briefing_id" value="<?php echo $briefing['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="btn btn-small">
                                        <option value="novo" <?php echo $briefing['status'] === 'novo' ? 'selected' : ''; ?>>Novo</option>
                                        <option value="em_andamento" <?php echo $briefing['status'] === 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                        <option value="concluido" <?php echo $briefing['status'] === 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>