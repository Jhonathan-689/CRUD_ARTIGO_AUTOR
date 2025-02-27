<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
$auth = new AuthController();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_GET['token'] ?? '';
    $newPassword = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    
    if ($newPassword === $confirmPassword) {
        $message = $auth->resetPassword($token, $newPassword);
    } else {
        $message = "As senhas não coincidem.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
</head>
<body>
    <h2>Redefinir Senha</h2>
    
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label for="password">Nova Senha:</label>
        <input type="password" name="password" required>
        
        <label for="confirm_password">Confirmar Senha:</label>
        <input type="password" name="confirm_password" required>
        
        <button type="submit">Redefinir</button>
    </form>
    
    <p><a href="login.php">Voltar ao login</a></p>
</body>
</html>
