<?php
session_start();
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? 'info';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/CRUD_ARTIGO_AUTOR/public/css/style.css">
  <script defer src="/CRUD_ARTIGO_AUTOR/public/js/form-validation.js"></script>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="container">
    <div class="card p-4 shadow-lg" style="max-width: 400px; margin: auto;">
      <h2 class="text-center mb-4">Login</h2>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($message); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form method="POST" action="../controllers/LoginController.php">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail:</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Senha:</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>

        <div class="mt-2 text-center">
          <a href="forgot_password.php" class="text-decoration-none">Esqueci minha senha</a>
        </div>
      </form>
      <br>
      <p class="text-center">Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
    </div>
  </div>
</body>

</html>