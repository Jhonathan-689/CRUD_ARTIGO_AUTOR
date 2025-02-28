<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <!-- Link para o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybW7JfS3f0lTq77tX5g2duyabvY5Q3i9j0LQG5z6Mkj3JP1t8L" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 shadow-lg">
                    <h2 class="text-center mb-4">Redefinir Senha</h2>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="../controllers/ResetPasswordController.php">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

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

                    <div class="mt-3 text-center">
                        <p><a href="../views/login.php" class="text-decoration-none">Voltar ao login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</body>

</html>
