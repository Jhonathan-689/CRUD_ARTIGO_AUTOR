<?php
require_once __DIR__ . '/CRUD ARTIGO_AUTOR/config/db_connect.php';

class ArticleAuthorModel
{
  private $conn;

  public function __construct()
  {
    $database = new Db_Connect();
    $this->conn = $database->connect();
  }

  // Associar um autor a um artigo
  public function associateAuthorToArticle($article_id, $author_id)
  {
    $sql = "INSERT INTO articles_authors (articles_id, authors_id) VALUES (:articles_id, :author_id)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':articles_id', $article_id);
    $stmt->bindParam(':authors_id', $author_id);
    return $stmt->execute();
  }

  // Obter autores de um artigo expecifico
  public function getAuthorsByArticle($article_id)
  {
    $sql = "SELECT a.id, a.name, a.email FROM authors a
            JOIN articles_authors aa ON a.id = aa.authors_id
            WHERE aa.articles_id = :article_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':articles_id', $article_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Obter artigos de um autor específico
  public function getArticlesByAuthor($author_id)
  {
    $sql = "SELECT ar.id, ar.title, ar.content FROM articles ar
            JOIN articles_authors aa ON ar.id = aa.articles_id
            WHERE aa.authors_id = :author_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':author_id', $author_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Remover um autor de um artigo
  public function removeAuthorFromArticle($article_id, $author_id)
  {
    $sql = "DELETE FROM articles_authors WHERE articles_id = :articles_id AND authors_id = :authors_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':author_id', $author_id);
    return $stmt->execute();
  }
}


?>