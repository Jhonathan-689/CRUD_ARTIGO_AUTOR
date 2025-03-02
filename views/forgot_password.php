<?php
session_start();

// Verifica se há mensagem na sessão antes de apagá-la
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? 'info';

// Apaga a mensagem da sessão somente depois de exibi-la
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="card p-4 shadow-lg" style="max-width: 400px; margin: auto;">
            <h2 class="text-center mb-4">Recuperação de Senha</h2>

            <!-- Exibir mensagem de erro ou sucesso -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="../controllers/ForgotPasswordController.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Digite seu e-mail:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Enviar</button>
            </form>

            <p class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Voltar ao login</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
