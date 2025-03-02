<?php
session_start();
require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['message'] = "Preencha todos os campos!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "As senhas não coincidem!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/register.php");
        exit();
    }

    $result = $auth->register($name, $email, $password, $role);

    if ($result === true) {
        $_SESSION['message'] = "Cadastro realizado com sucesso! Verifique o e-mail para ativar sua conta.";
        $_SESSION['message_type'] = "success";
        header("Location: ../views/login.php");
        exit();
    } else {
        $_SESSION['message'] = "Erro ao cadastrar: " . htmlspecialchars($result);
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/register.php");
        exit();
    }
}
