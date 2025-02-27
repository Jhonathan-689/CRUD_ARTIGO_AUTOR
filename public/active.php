<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();
$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    error_log("Token recebido no active.php: " . $token);

    $activated = $auth->activateAccount($token);

    if ($activated) {
        error_log("Conta ativada com sucesso.");
        header("Location: login.php");
        exit();
    } else {
        error_log("Falha ao ativar conta com token: " . $token);
        $message = "Erro: Token inválido ou conta já ativada.";
    }
} else {
    error_log("Nenhum token foi recebido no active.php.");
    $message = "Token inválido ou não fornecido.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ativação de Conta</title>
</head>
<body>
    <h2>Ativação de Conta</h2>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="login.php">Ir para o Login</a>
</body>
</html>
