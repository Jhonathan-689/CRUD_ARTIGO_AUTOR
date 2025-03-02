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
    $sql = "(SELECT id, name, password, is_active, 'user' AS role FROM users WHERE email = :email)
            UNION ALL
            (SELECT id, name, password, is_active, 'author' AS role FROM authors WHERE email = :email)
            LIMIT 1";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("Usuário encontrado: " . print_r($user, true));

    if (!$user) {
      error_log("Nenhum usuário encontrado para o email: " . $email);
      return "Credenciais incorretas, tente novamente!";
    }

    if (empty($user['password'])) {
      error_log("Senha vazia ou NULL no banco para o usuário ID: " . $user['id']);
      return "Credenciais incorretas, tente novamente!";
    }

    if (!password_verify($password, $user['password'])) {
      error_log("Senha incorreta para o usuário ID: " . $user['id']);
      return "Credenciais incorretas, tente novamente!";
    }

    if ($user['is_active'] == 0) {
      error_log("Conta não ativada para o usuário ID: " . $user['id']);
      return "Conta não ativada. Verifique seu e-mail.";
    }

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

    $stmtCheck = $this->conn->prepare("SELECT id FROM users WHERE token = :token");
    $stmtCheck->bindParam(':token', $token);
    $stmtCheck->execute();
    $userExists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$userExists) {
      error_log("ERRO: Token não encontrado na tabela 'users' para redefinição de senha.");
      return false;
    }

    $sql = "UPDATE users SET password = :password, token = NULL WHERE token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);

    $executed = $stmt->execute();
    $rowsAffected = $stmt->rowCount();

    error_log("Tentativa de redefinição de senha para usuário. Token: " . $token);
    error_log("Senha criptografada: " . $hashedPassword);
    error_log("Linhas afetadas: " . $rowsAffected);

    return $rowsAffected > 0;
  }

  public function getUserByEmail($email)
  {
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: false;
  }

  public function getUserByToken($token)
  {
    $sql = "SELECT * FROM users WHERE token = :token LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
  }

}
