<?php

require_once __DIR__ . '/../config/db_connect.php';

class ArticleModel
{
  private $conn;

  public function __construct()
  {
    $database = new Db_Connect();
    $this->conn = $database->connect();
  }

  // Criar um novo artigo
  public function createArticle($title, $content)
  {
    $sql = "INSERT INTO articles (title, content, created_at) VALUES (:title, :content, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);

    if ($stmt->execute()) {
      return $this->conn->lastInsertId(); // Retorna o ID do artigo recém-criado
    }

    return false;
  }

  // Obter todos os artigos
  public function getAllArticles()
  {
    $sql = "SELECT a.id, a.title, a.content, a.created_at, au.name AS author_name 
            FROM articles a
            JOIN articles_authors aa ON a.id = aa.article_id
            JOIN authors au ON aa.author_id = au.id
            WHERE au.is_active = 1
            ORDER BY a.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getArticlesByAuthor($author_id)
  {
    $sql = "SELECT a.id, a.title, a.content, a.created_at 
            FROM articles a
            JOIN articles_authors aa ON a.id = aa.article_id
            WHERE aa.author_id = :author_id
            ORDER BY a.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Obter um artigo pelo id
  public function getArticleById($id)
  {
    $sql = "SELECT * FROM articles WHERE id = :id"; // Certifique-se de que a query está correta
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Garante que o ID é tratado como número inteiro
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Atualizar informações do artigo

  public function updateArticles($id, $title, $content)
{
    $sql = "UPDATE articles SET title = :title, content = :content WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);

    return $stmt->execute();
}


  // Excluir um artigo
  public function deleteArticle($id)
  {
    $sql = "DELETE FROM articles WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }
}

?>