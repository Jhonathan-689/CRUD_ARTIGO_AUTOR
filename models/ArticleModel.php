<?php

require_once __DIR__ . '/CRUD ARTIGO_AUTOR/config/db_connect.php';

class ArticleModel{
  private $conn;

  public function __construct(){
    $database = new Db_Connect();
    $this->conn = $database->connect();
  }

  // Criar um novo artigo
  public function createArticle($title, $content){
    $sql = 'INSERT INTO articles (title, content, created_at) VALUES (:title, :content, NOW())';
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    return $stmt->execute();
  }

  // Obter todos os artigos
  public function getAllArticles(){
    $sql = "SELECT * FROM articles ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Obter um artigo pelo id
  public function getArticleById($id){
    $sql = "SELECT * FROM articles WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Atualizar informações do artigo

  public function updateArticles($id, $title, $content){
    $sql = "UPDATE articles SET title = :title, content = :content WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    return  $stmt->execute();
  }

  // Excluir um artigo
  public function deleteArticle($id){
    $sql = "DELETE FROM articles WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }
}

?>