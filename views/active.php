<?php if (!isset($message)) $message = ''; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ativação de Conta</title>
</head>
<body>
    <h2>Ativação de Conta</h2>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="../views/login.php">Ir para o Login</a>
</body>
</html>
