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

  // Fechar conexão
  public function closeConnection()
  {
    $this->conn = null;
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
    $result = $stmt->execute();

    $this->closeConnection(); // Fecha a conexão após a operação
    return $result;
  }

  public function emailExists($email)
  {
    $sql = "SELECT id FROM users WHERE email = :email
            UNION
            SELECT id FROM authors WHERE email = :email";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;

    $this->closeConnection(); // Fecha a conexão
    return $result;
  }

  public function activateAccount($token)
  {
    $sql = "UPDATE users SET is_active = 1, token = NULL WHERE token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $result = $stmt->execute();

    $this->closeConnection();
    return $result;
  }

  public function verifyLogin($email, $password)
  {
    $sql = "SELECT id, name, password, is_active, 'user' AS role FROM users WHERE email = :email
            UNION
            SELECT id, name, NULL AS password, 1 AS is_active, 'author' AS role FROM authors WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || ($user['role'] === 'user' && !password_verify($password, $user['password']))) {
      $this->closeConnection();
      return "Credenciais incorretas, tente novamente!";
    }

    if ($user['is_active'] == 0) {
      $this->closeConnection();
      return "Conta não ativada. Verifique seu e-mail.";
    }

    $this->closeConnection();
    return $user;
  }

  public function generateResetToken($email, $token)
  {
    $sql = "UPDATE users SET token = :token WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':email', $email);
    $result = $stmt->execute();

    $this->closeConnection();
    return $result;
  }

  public function resetPassword($token, $newPassword)
  {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = 'UPDATE users SET password = :password, token = NULL WHERE token = :token';
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);
    $result = $stmt->execute();

    $this->closeConnection();
    return $result;
  }
}
?>
