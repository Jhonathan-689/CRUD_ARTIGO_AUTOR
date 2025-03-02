<?php
session_start();
require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Digite um e-mail válido.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Chama o método e recebe a mensagem correta
        $message = $auth->forgotPassword($email);
        $_SESSION['message'] = $message;
        
        // Define o tipo de mensagem com base no conteúdo
        $_SESSION['message_type'] = ($message === "Um e-mail foi enviado com instruções para redefinir sua senha.") 
            ? "success" 
            : "danger";
    }

    header("Location: ../views/forgot_password.php");
    exit();
}
