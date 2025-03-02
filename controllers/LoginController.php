<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../views/dashboard.php");
    exit();
}

require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $message = $auth->login($email, $password);

    if ($message === "Login bem-sucedido!") {
        $_SESSION['message'] = "Login realizado com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: ../views/dashboard.php");
        exit();
    } else {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/login.php");
        exit();
    }
}
