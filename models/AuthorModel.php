<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthorModel
{
  private $conn;
  private $userModel;

  public function __construct()
  {
    $database = new Db_Connect;
    $this->conn = $database->connect();
    $this->userModel = new UserModel();
  }

  public function createAuthor($name, $email, $password, $token)
  {
    try {
      if (!$this->conn) {
        throw new Exception("Erro na conexão com o banco de dados.");
      }

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $sql = "INSERT INTO authors (name, email, password, token, is_active, created_at) VALUES (:name, :email, :password, :token, 0, NOW())";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':password', $hashedPassword);
      $stmt->bindParam(':token', $token);
      return $stmt->execute();

    } catch (Exception $e) {
      error_log($e->getMessage());
      return false;
    }
  }

  public function activateAccount($token)
  {
    error_log("Verificando token no banco para ativação do autor: " . $token);

    $sql = "UPDATE authors SET is_active = 1, token = NULL WHERE token = :token AND is_active = 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);

    if ($stmt->execute()) {
      $rowsAffected = $stmt->rowCount();
      error_log("Linhas afetadas na ativação do autor: " . $rowsAffected);
      return $rowsAffected > 0;
    } else {
      error_log("Erro ao executar a query de ativação do autor.");
      return false;
    }
  }

  public function generateResetToken($email, $token)
  {
    $sql = "UPDATE authors SET token = :token WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
  }

  public function resetPassword($token, $newPassword)
  {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmtCheck = $this->conn->prepare("SELECT id FROM authors WHERE token = :token");
    $stmtCheck->bindParam(':token', $token);
    $stmtCheck->execute();
    $authorExists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$authorExists) {
      error_log("ERRO: Token não encontrado na tabela 'authors' para redefinição de senha.");
      return false;
    }

    $sql = "UPDATE authors SET password = :password, token = NULL WHERE token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);

    $executed = $stmt->execute();
    $rowsAffected = $stmt->rowCount();

    error_log("Tentativa de redefinição de senha para autor. Token: " . $token);
    error_log("Senha criptografada: " . $hashedPassword);
    error_log("Linhas afetadas: " . $rowsAffected);

    return $rowsAffected > 0;
  }


  public function getAllAuthors()
  {
    $sql = "SELECT * FROM authors ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAuthorById($id)
  {
    $sql = "SELECT * FROM authors WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function associateAuthorToArticle($article_id, $author_id)
  {
    $sql = "INSERT INTO articles_authors (article_id, author_id) VALUES (:article_id, :author_id)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':author_id', $author_id);
    return $stmt->execute();
  }

  public function updateAuthor($id, $name, $email)
  {
    $sql = "UPDATE authors SET name = :name, email = :email WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
  }

  public function deleteAuthor($id)
  {
    $sql = "DELETE FROM authors WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }
  public function getAuthorByToken($token)
  {
    $sql = "SELECT * FROM authors WHERE token = :token LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
  }

  public function getAuthorByEmail($email)
  {
    $sql = "SELECT * FROM authors WHERE email = :email LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
  }

}
