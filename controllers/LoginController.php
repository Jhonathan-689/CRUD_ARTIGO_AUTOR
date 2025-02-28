<?php
session_start();

// Se o usuário já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../views/dashboard.php");
    exit();
}

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $message = $auth->login($email, $password);

    if ($message === "Login bem-sucedido!") {
        header("Location: ../views/dashboard.php");
        exit();
    }
}

// Inclui a view do formulário de login
require_once __DIR__ . '/../views/login.php';
