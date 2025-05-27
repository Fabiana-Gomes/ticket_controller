<?php
session_start();
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, senha FROM clientes WHERE login = :login LIMIT 1");
    $stmt->execute([':login' => $user]);
    $cliente = $stmt->fetch();

    if ($cliente && $pass === $cliente['senha']) {
        $_SESSION['authenticated'] = true;
        $_SESSION['cliente_id'] = $cliente['id'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha inválidos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Tickets</title>
    
 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="container login-container">
        <div class="card login-card">
            <div class="card-header">
                <i class="bi bi-shield-lock logo-icon"></i>
                <h1 class="card-title">ACESSO RESTRITO</h1>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-4" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" novalidate>
                    <div class="mb-4">
                        <label for="username" class="form-label">Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-person-fill text-secondary"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Digite seu usuário" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-lock-fill text-secondary"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i> ENTRAR NO SISTEMA
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
