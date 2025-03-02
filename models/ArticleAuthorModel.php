<?php
require_once __DIR__ . '/../config/db_connect.php';

class ArticleAuthorModel
{
  private $conn;

  public function __construct()
  {
    $database = new Db_Connect();
    $this->conn = $database->connect();
  }

  public function associateAuthorToArticle($article_id, $author_id, $is_coauthor = 1)
  {
    $sql = "INSERT INTO articles_authors (article_id, author_id, is_coauthor) VALUES (:article_id, :author_id, :is_coauthor)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    $stmt->bindParam(':is_coauthor', $is_coauthor, PDO::PARAM_INT);
    return $stmt->execute();
  }


  public function getAuthorsByArticle($article_id)
{
    $sql = "SELECT a.id, a.name, aa.is_coauthor 
              FROM authors a
              JOIN articles_authors aa ON a.id = aa.author_id
              WHERE aa.article_id = :article_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

  public function getArticlesByAuthor($author_id)
  {
    $sql = "SELECT ar.id, ar.title, ar.content
            FROM articles ar
            JOIN articles_authors aa ON ar.id = aa.article_id
            WHERE aa.author_id = :author_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function removeAuthorFromArticle($article_id, $author_id)
  {
    $sql = "DELETE FROM articles_authors WHERE article_id = :article_id AND author_id = :author_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    return $stmt->execute();
  }
}
?>