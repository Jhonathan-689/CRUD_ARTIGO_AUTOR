<?php
require_once __DIR__ . '/CRUD ARTIGO_AUTOR/config/db_connect.php';

class UserModel
{
  private $conn;

  public function __construct()
  {
    $database = new Db_Connect;
    $this->conn = $database->connect();
  }

  // cadastrar novo usúario
  public function registerUser($name, $email, $password, $token)
  {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users(name, email, password, token, is_active, create_at) VALUES (:name, :email, :password, :token, 0, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();
  }

  // ativar conta pelo token

  public function activateAccont($token)
  {
    $sql = "UPDATE users SET is_active = 1, token = NULL WHERE token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();

  }

  // verificar login do usúario

  public function verifyLogin($email, $password)
  {
    $sql = "SELECT id, name, password, is_active FROM users WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      if ($user['is_active'] == 1) {
        return $user;
      } else {
        return 'conta inativa';
      }
    }
    return false;

  }

  // gerar token de recuperação de senha

  public function generateResetToken($email, $token)
  {
    $sql = "UPDATE users SET token = :token WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
  }

  // Redefinir senha

  public function resetPassword($token, $newPassword)
  {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = 'UPDATE users SET password = :password, token = NULL WHERE token = :token';
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();
  }

}








?>