<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Link para o Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/CRUD_ARTIGO_AUTOR/public/css/style.css">
  <script defer src="/CRUD_ARTIGO_AUTOR/public/js/form-validation.js"></script>
  <!-- Scripts do Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz4fnFO9gybW7JfS3f0lTq77tX5g2duyabvY5Q3i9j0LQG5z6Mkj3JP1t8L"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-pzjw8f+ua7Kw1TIq0snFxqT2fbGzAt2XSLmHuw2VmiYbCVgAD36D9GO9Yw8fK2gV"
    crossorigin="anonymous"></script>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="container">
    <div class="card p-4 shadow-lg" style="max-width: 400px; margin: auto;">
      <h2 class="text-center mb-4">Login</h2>

      <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
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

        <!-- Link de 'Esqueci minha senha' agora abaixo do botão -->
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