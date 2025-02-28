<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();
$message = ''; // Inicializa a variável para evitar warnings

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    error_log("Token recebido no ActivateController.php: " . $token);

    $activated = $auth->activateAccount($token);

    if ($activated) {
        error_log("Conta ativada com sucesso.");
        header("Location: ../views/login.php");
        exit();
    } else {
        error_log("Falha ao ativar conta com token: " . $token);
        $message = "Erro: Token inválido ou conta já ativada.";
    }
} else {
    error_log("Nenhum token foi recebido no ActivateController.php.");
    $message = "Token inválido ou não fornecido.";
}

// Passar a variável para a view
require_once __DIR__ . '/../views/active.php';
