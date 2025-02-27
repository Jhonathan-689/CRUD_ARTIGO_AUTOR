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

  // Criar um novo autor
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
    $sql = "UPDATE authors SET password = :password, token = NULL WHERE token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();
}


  // Obter todos os autores
  public function getAllAuthors()
  {
    $sql = "SELECT * FROM authors ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Obter um autor pelo ID
  public function getAuthorById($id) {
    $sql = "SELECT * FROM authors WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Atualizar informações do autor
  public function updateAuthor($id, $name, $email){
    $sql = "UPDATE authors SET name = :name, email = :email WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
  }

  // Excluir um autor
  public function deleteAuthor($id){
    $sql = "DELETE FROM authors WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }
}
