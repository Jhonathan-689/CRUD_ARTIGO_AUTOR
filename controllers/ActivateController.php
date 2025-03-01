<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();
$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    error_log("Token recebido no ActivateController.php: " . $token);

    $activated = $auth->activateAccount($token);

    if ($activated) {
        error_log("Conta ativada com sucesso.");
        $_SESSION['message'] = "Conta ativada com sucesso! Agora você pode fazer login.";
        $_SESSION['message_type'] = "success";
        header("Location: ../views/login.php");
        exit();
    } else {
        error_log("Falha ao ativar conta com token: " . $token);
        $_SESSION['message'] = "Erro: Token inválido ou conta já ativada.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/active.php");
        exit();
    }
} else {
    error_log("Nenhum token foi recebido no ActivateController.php.");
    $_SESSION['message'] = "Token inválido ou não fornecido.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../views/active.php");
    exit();
}
