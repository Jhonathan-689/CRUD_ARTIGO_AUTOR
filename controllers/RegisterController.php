<?php
session_start();

// Se o usuário já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /CRUD_ARTIGO_AUTOR/views/dashboard.php");
    exit();
}

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $message = "Preencha todos os campos!";
    } elseif ($password !== $confirm_password) {
        $message = "As senhas não coincidem!";
    } else {
        $result = $auth->register($name, $email, $password, $role);

        error_log("Resultado do registro: " . print_r($result, true));

        if ($result === true) {
            header("Location: /CRUD_ARTIGO_AUTOR/views/login.php"); // Redireciona após sucesso
            exit();
        } else {
            $message = "Erro ao cadastrar: " . htmlspecialchars($result);
        }
    }
}

// Inclui a view do formulário de cadastro
require_once __DIR__ . '/../views/register.php';
