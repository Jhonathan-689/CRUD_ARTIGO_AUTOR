<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../public/css/style.css">
    <script defer src="../public/js/form-validation.js"></script>
</head>

<body>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-center mb-4">Cadastro</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form id="registerForm" method="POST" action="../controllers/RegisterController.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="error-message">Nome é obrigatório.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="error-message">Insira um e-mail válido.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="error-message">A senha deve ter pelo menos 6 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirme a Senha:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            required>
                        <div class="error-message">As senhas não coincidem.</div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Tipo de Conta:</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">Usuário</option>
                            <option value="author">Autor</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                </form>

                <p class="text-center mt-3">Já tem uma conta? <a href="../views/login.php">Faça login</a></p>
            </div>
        </div>
    </div>
</body>

</html>