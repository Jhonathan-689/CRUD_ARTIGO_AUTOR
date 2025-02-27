<?php
require_once __DIR__ . '/../config/db_connect.php';

class AuthorModel
{
  private $conn;
  private $userModel;
  private $authorModel;

  public function __construct()
  {
    
    $this->userModel = new UserModel();
    $this->authorModel = new AuthorModel();
  }
  
  public function closeConnection()
  {
    $this->conn = null;
  }


  // Criar um novo autor
  public function createAuthor($name, $email)
  {
    $sql = "INSERT INTO authors (name, email, created_at) VALUES (:name, :email, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();

    $this->closeConnection();
    return $result;
  }
  public function activateAccount($token)
{
    $sql = "UPDATE authors SET is_active = 1 WHERE activation_token = :token";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    return $stmt->execute();

    $this->closeConnection();
    return $result;
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


  // atualizar informações do autor
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

?>