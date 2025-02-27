<?php
session_start();

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = $auth->forgotPassword($email);
    } else {
        $message = "Digite um e-mail válido.";
    }
}

// Inclui a view do formulário de recuperação de senha
require_once __DIR__ . '/../views/forgot_password.php';