<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("location: dashboard.php");
  exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once __DIR__ . '/../controllers/AuthController.php';
  $auth = new AuthController();

  $email = $_POST['email'];
  $password = $_POST['password'];

  $message = $auth->login($email, $password);

  if ($message === "Login bem-sucedido!") {
    header("Location: dashboard.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>

<body>
  <h2>Login</h2>

  <?php if ($message): ?>
    <p><?php echo $message; ?></p>
  <?php endif; ?>

  <form method="POST">
    <label for="email">E-mail:</label>
    <input type="email" name="email" required>

    <label for="password">Senha:</label>
    <input type="password" name="password" required>

    <button type="submit">Entrar</button>
  </form>

  <p><a href="forgot_password.php">Esqueci minha senha</a></p>
</body>

</html>