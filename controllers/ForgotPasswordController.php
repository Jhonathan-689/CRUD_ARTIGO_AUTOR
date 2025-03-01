<?php
session_start();
require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = $auth->forgotPassword($email);
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Digite um e-mail válido.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: ../views/forgot_password.php");
    exit();
}