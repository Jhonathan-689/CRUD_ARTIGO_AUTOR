<?php
session_start();
require_once __DIR__ . '/../models/AuthorModel.php';
require_once __DIR__ . '/../models/UserModel.php';

$authorModel = new AuthorModel();
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        $_SESSION['message'] = "Token inválido.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/forgot_password.php");
        exit();
    }

    $user = $userModel->getUserByToken($token);
    $author = $authorModel->getAuthorByToken($token);

    if (!$user && !$author) {
        error_log("ERRO: Token inválido ou expirado.");
        $_SESSION['message'] = "Token inválido ou expirado.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/forgot_password.php");
        exit();
    }

    // Armazena o token na sessão e remove da URL
    $_SESSION['reset_token'] = $token;
    header("Location: ../views/reset_password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_SESSION['reset_token'] ?? '';
    $newPassword = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($token)) {
        error_log("ERRO: Token de redefinição vazio.");
        $_SESSION['message'] = "Token inválido.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/forgot_password.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        error_log("ERRO: Senhas não coincidem.");
        $_SESSION['message'] = "As senhas não coincidem.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/reset_password.php");
        exit();
    }

    // Verifica se o token pertence a um user ou author antes de atualizar
    error_log("Tentando redefinir senha para users com token: " . $token);
    $passwordUpdated = $userModel->resetPassword($token, $newPassword);

    if (!$passwordUpdated) {
        error_log("Redefinição falhou para users, tentando para authors...");
        $passwordUpdated = $authorModel->resetPassword($token, $newPassword);
    }

    if ($passwordUpdated) {
        unset($_SESSION['reset_token']);
        error_log("Senha redefinida com sucesso!");
        $_SESSION['message'] = "Senha redefinida com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: ../views/login.php");
        exit();
    } else {
        error_log("ERRO: Token inválido ou expirado.");
        $_SESSION['message'] = "Token inválido ou expirado.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/reset_password.php");
        exit();
    }
}
