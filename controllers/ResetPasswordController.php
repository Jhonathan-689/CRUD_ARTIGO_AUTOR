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

    // Verifica se o token é válido para usuários ou autores
    $user = $userModel->getUserByToken($token);
    $author = $authorModel->getAuthorByToken($token);

    if (!$user && !$author) {
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
        $_SESSION['message'] = "Token inválido.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/forgot_password.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['message'] = "As senhas não coincidem.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/reset_password.php");
        exit();
    }

    // Atualiza a senha do usuário ou do autor
    $passwordUpdated = $userModel->resetPassword($token, $newPassword) || $authorModel->resetPassword($token, $newPassword);

    if ($passwordUpdated) {
        // Remove o token da sessão após o uso
        unset($_SESSION['reset_token']);

        $_SESSION['message'] = "Senha redefinida com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: ../views/login.php");
        exit();
    } else {
        $_SESSION['message'] = "Token inválido ou expirado.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/reset_password.php");
        exit();
    }
}
