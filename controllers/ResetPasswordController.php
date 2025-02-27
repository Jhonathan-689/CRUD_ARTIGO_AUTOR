<?php
session_start();

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();
$message = '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($token)) {
        $message = "Token inválido ou expirado.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "As senhas não coincidem.";
    } else {
        $result = $auth->resetPassword($token, $newPassword);
        if ($result === "Senha redefinida com sucesso!") {
            header("Location: /CRUD_ARTIGO_AUTOR/views/login.php");
            exit(); // Garante que o redirecionamento seja imediato
        } else {
            $message = $result;
        }
    }
}

// Inclui a view do formulário de redefinição de senha
require_once __DIR__ . '/../views/reset_password.php';
