<?php
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();

    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = $auth->forgotPassword($email);
    } else {
        $message = "Digite um e-mail válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha</title>
</head>
<body>
    <h2>Recuperação de Senha</h2>

    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Digite seu e-mail:</label>
        <input type="email" name="email" required>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="login.php">Voltar ao login</a></p>
</body>
</html>
