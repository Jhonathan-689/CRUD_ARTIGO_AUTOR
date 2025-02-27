<?php
  session_start();
  if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
  }

  $message = '';

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    

    if(empty($name) || empty($email) || empty($password) || empty($role)){
      $message = "Preencha todos os campos!";
    } elseif ($password !== $confirm_password){
      $message = "As senhas não coincidem!";
    } else{
      $message = $auth->register($name, $email, $password, $role);
    }
  }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
</head>
<body>
    <h2>Cadastro</h2>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Nome:</label>
        <input type="text" name="name" required>

        <label for="email">E-mail:</label>
        <input type="email" name="email" required>

        <label for="password">Senha:</label>
        <input type="password" name="password" required>

        <label for="confirm_password">Confirme a Senha:</label>
        <input type="password" name="confirm_password" required>

        <label for="role">Tipo de Conta:</label>
        <select name="role" required>
            <option value="user">Usuário Comum</option>
            <option value="author">Autor</option>
        </select>

        <button type="submit">Cadastrar</button>
    </form>

    <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
</body>
</html>
</html>