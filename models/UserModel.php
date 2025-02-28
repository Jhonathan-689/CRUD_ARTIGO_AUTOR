<?php
require_once __DIR__ . '/../config/db_connect.php';

class UserModel
{
  private $conn;

  public function __construct()
  {
    $database = new Db_Connect;
    $this->conn = $database->connect();
  }

  // cadastrar novo usuário
  public function registerUser($name, $email, $password, $token)
  {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users(name, email, password, token, is_active, created_at) VALUES (:name, :email, :password, :token, 0, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();
  }

  public function emailExists($email)
  {
    $sql = "SELECT id FROM users WHERE email = :email
            UNION
            SELECT id FROM authors WHERE email = :email";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
  }

  public function activateAccount($token)
  {
    error_log("Verificando token no banco para ativação do usuário: " . $token);

    $sql = "UPDATE users SET is_active = 1, token = NULL WHERE token = :token AND is_active = 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);

    if ($stmt->execute()) {
      $rowsAffected = $stmt->rowCount();
      error_log("Linhas afetadas na ativação do usuário: " . $rowsAffected);
      return $rowsAffected > 0;
    } else {
      error_log("Erro ao executar a query de ativação do usuário.");
      return false;
    }
  }

  public function verifyLogin($email, $password)
  {
    // A consulta SQL seleciona os dados de usuários e autores baseados no e-mail
    $sql = "SELECT id, name, password, is_active, 'user' AS role FROM users WHERE email = :email
            UNION
            SELECT id, name, password, is_active, 'author' AS role FROM authors WHERE email = :email";

    // Prepara e executa a consulta
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Busca os dados do usuário ou autor
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado
    if (!$user) {
      return "Credenciais incorretas, tente novamente!";
    }

    // Verifica se o usuário é um 'user' e valida a senha
    if ($user['role'] === 'user' && !password_verify($password, $user['password'])) {
      return "Credenciais incorretas, tente novamente!";
    }

    // Verifica se o autor tem senha (se autor não tiver senha, a verificação pode ser ignorada)
    if ($user['role'] === 'author' && !empty($user['password']) && !password_verify($password, $user['password'])) {
      return "Credenciais incorretas, tente novamente!";
    }

    // Verifica se a conta está ativada
    if ($user['is_active'] == 0) {
      return "Conta não ativada. Verifique seu e-mail.";
    }

    // Retorna os dados do usuário ou autor se tudo estiver correto
    return $user;
  }


  public function generateResetToken($email, $token)
  {
    $sql = "UPDATE users SET token = :token WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
  }

  public function resetPassword($token, $newPassword)
  {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = :password, token = NULL WHERE token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();
  }
}
