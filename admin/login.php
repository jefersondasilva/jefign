<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../backend/classes/AuthManager.php';

$auth = new AuthManager();


if ($auth->isAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
   
    if (!$auth->verifyCSRFToken($csrf_token)) {
        $error = 'Token de segurança inválido';
    } else {
        $result = $auth->login($username, $password);
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$csrf_token = $auth->generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - JEFIGN</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-2);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--brand-blue), var(--indigo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--sky);
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--paper);
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(54, 107, 179, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-blue), var(--indigo));
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff6b7a;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #51cf66;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: var(--muted);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>JEFIGN</h1>
            <p style="color: var(--muted); margin-top: 8px;">Área Administrativa</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
        
        <div class="footer">
            <p>Sistema protegido por autenticação</p>
        </div>
    </div>
</body>
</html>