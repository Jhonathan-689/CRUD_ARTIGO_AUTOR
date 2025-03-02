<?php
session_start();
$token = $_SESSION['reset_token'] ?? '';

if (empty($token)) {
    $_SESSION['message'] = "Token inválido.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../views/forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="card p-4 shadow-lg" style="max-width: 400px; margin: auto;">
            <h2 class="text-center mb-4">Redefinir Senha</h2>

            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <form method="POST" action="../controllers/ResetPasswordController.php">
                <div class="mb-3">
                    <label for="password" class="form-label">Nova Senha:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Senha:</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Redefinir</button>
            </form>

            <p class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Voltar ao login</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
