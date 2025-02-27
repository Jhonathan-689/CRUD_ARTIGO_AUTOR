<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redireciona para login se não estiver logado
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Bem-vindo, <?php echo $_SESSION['user_name']; ?>!</h2>
    <p>Você está logado como: <?php echo $_SESSION['role'] === 'user' ? 'Usuário Comum' : 'Autor'; ?></p>

    <p><a href="logout.php">Sair</a></p>
</body>
</html>