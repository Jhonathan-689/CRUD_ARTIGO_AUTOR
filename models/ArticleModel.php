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

  public function createArticle($title, $content, $author_id, $coauthors = [])
  {
    $sql = "INSERT INTO articles (title, content, created_at) VALUES (:title, :content, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);

    if ($stmt->execute()) {
      $article_id = $this->conn->lastInsertId();

      // Insere o autor principal
      $sql = "INSERT INTO articles_authors (article_id, author_id, is_coauthor) VALUES (:article_id, :author_id, 0)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
      $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
      $stmt->execute();

      // Insere os coautores se houver
      if (!empty($coauthors)) {
        $this->associateAuthorsToArticle($article_id, $coauthors);
      }

      return $article_id;
    }
    return false;
  }

  public function associateAuthorsToArticle($article_id, $coauthors)
  {
    $sql = "INSERT INTO articles_authors (article_id, author_id, is_coauthor) VALUES (:article_id, :author_id, 1)";
    $stmt = $this->conn->prepare($sql);

    foreach ($coauthors as $coauthor_id) {
      $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
      $stmt->bindValue(':author_id', $coauthor_id, PDO::PARAM_INT);
      $stmt->execute();
    }
  }

  public function getAllArticles()
  {
    $sql = "SELECT a.id, a.title, a.content, a.created_at, 
                   GROUP_CONCAT(DISTINCT au.name ORDER BY au.name SEPARATOR ', ') AS author_names
            FROM articles a
            JOIN articles_authors aa ON a.id = aa.article_id
            JOIN authors au ON aa.author_id = au.id
            WHERE au.is_active = 1
            GROUP BY a.id
            ORDER BY a.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getArticlesByAuthor($author_id)
  {
    $sql = "SELECT DISTINCT a.id, a.title, a.content, a.created_at
            FROM articles a
            JOIN articles_authors aa ON a.id = aa.article_id
            WHERE aa.author_id = :author_id
            ORDER BY a.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getArticleById($id)
  {
    $sql = "SELECT * FROM articles WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getUserArticles($author_id)
  {
    $sql = "SELECT a.id, a.title, a.content, a.created_at, a.last_edited_by, 
                     aa.is_coauthor,
                     COALESCE(auth.name, 'Autor original') AS last_edited_name
              FROM articles a
              JOIN articles_authors aa ON a.id = aa.article_id
              LEFT JOIN authors auth ON auth.id = a.last_edited_by
              WHERE aa.author_id = :author_id
              ORDER BY a.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function updateArticle($id, $title, $content, $new_coauthors, $main_author_id)
  {
    // 1) Atualiza o título e o conteúdo do artigo
    $sql = "UPDATE articles SET title = :title, content = :content, last_edited_by = :last_edited_by WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':last_edited_by', $main_author_id, PDO::PARAM_INT);
    $stmt->execute();

    // 2) Obtém os coautores atuais (exclui o autor principal)
    $old_coauthors = $this->getCoauthorsIdsByArticle($id);

    // 3) Garante que o autor principal não seja removido
    $new_coauthors = array_diff($new_coauthors, [$main_author_id]);

    // 4) Adiciona automaticamente o usuário que está editando para evitar que ele se remova sem querer
    if (!in_array($main_author_id, $new_coauthors)) {
      $new_coauthors[] = $main_author_id;
    }

    // 5) Calcula os coautores que devem ser removidos e adicionados
    $to_remove = array_diff($old_coauthors, $new_coauthors);
    $to_add = array_diff($new_coauthors, $old_coauthors);

    // 6) Remover os coautores antigos (com is_coauthor = 1)
    if (!empty($to_remove)) {
      $placeholders = implode(',', array_fill(0, count($to_remove), '?'));
      $sql = "DELETE FROM articles_authors
                WHERE article_id = ?
                  AND author_id IN ($placeholders)
                  AND is_coauthor = 1";  // Garante que só coautores são removidos
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array_merge([$id], array_values($to_remove)));
    }

    // 7) Adiciona os novos coautores apenas se ainda não existirem
    if (!empty($to_add)) {
      $sql = "INSERT INTO articles_authors (article_id, author_id, is_coauthor)
                SELECT :article_id, :author_id, 1 FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM articles_authors 
                    WHERE article_id = :article_id_check AND author_id = :author_id_check
                )";
      $stmt = $this->conn->prepare($sql);
      foreach ($to_add as $coauthor_id) {
        $stmt->bindValue(':article_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':author_id', $coauthor_id, PDO::PARAM_INT);
        $stmt->bindValue(':article_id_check', $id, PDO::PARAM_INT);
        $stmt->bindValue(':author_id_check', $coauthor_id, PDO::PARAM_INT);
        $stmt->execute();
      }
    }

    return true;
  }



  private function getCoauthorsIdsByArticle($article_id)
  {
    $sql = "SELECT author_id 
            FROM articles_authors 
            WHERE article_id = :article_id 
              AND is_coauthor = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_COLUMN);  // Retorna apenas os IDs dos coautores
  }

  public function removeAuthorFromArticle($article_id, $author_id)
  {
    $sql = "DELETE FROM articles_authors WHERE article_id = :article_id AND author_id = :author_id AND is_coauthor = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
    return $stmt->execute();
  }



  public function removeCoauthorsFromArticle($article_id, $main_author_id, $coauthors)
  {
    if (empty($coauthors)) {
      $sql = "DELETE FROM articles_authors 
                WHERE article_id = :article_id 
                  AND author_id != :main_author_id 
                  AND is_coauthor = 1";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
      $stmt->bindParam(':main_author_id', $main_author_id, PDO::PARAM_INT);
    } else {
      // Remove apenas os coautores que não estão mais na lista enviada
      $placeholders = implode(',', array_fill(0, count($coauthors), '?'));
      $sql = "DELETE FROM articles_authors 
                WHERE article_id = ? 
                  AND author_id NOT IN ($placeholders) 
                  AND is_coauthor = 1";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array_merge([$article_id], $coauthors));
    }
    return $stmt->execute();
  }
  public function deleteArticle($id)
  {
    $sql = "DELETE FROM articles WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }

  public function getPaginatedArticles($limit, $offset)
  {
    $sql = "SELECT a.id, a.title, a.content, a.created_at, 
                   GROUP_CONCAT(DISTINCT au.name ORDER BY au.name SEPARATOR ', ') AS author_names
            FROM articles a
            JOIN articles_authors aa ON a.id = aa.article_id
            JOIN authors au ON aa.author_id = au.id
            WHERE au.is_active = 1
            GROUP BY a.id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countAllArticles()
  {
    $sql = "SELECT COUNT(*) AS total FROM articles";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
  }
}

?>