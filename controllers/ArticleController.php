<?php

require_once __DIR__ . '/../models/ArticleModel.php';

class ArticleController
{
  private $articleModel;

  public function __construct()
  {
    $this->articleModel = new ArticleModel();
  }

  // Criar um novo artigo
  public function create($title, $content)
{
    session_start();
    $author_id = $_SESSION['user_id']; // Obtém o ID do autor logado

    if (!empty($title) && !empty($content) && !empty($author_id)) {
        // Criar o artigo e obter o ID gerado
        $article_id = $this->articleModel->createArticle($title, $content);

        if ($article_id) {
            // Associar o artigo ao autor na tabela articles_authors
            require_once __DIR__ . '/../models/ArticleAuthorModel.php';
            $articleAuthorModel = new ArticleAuthorModel();
            $articleAuthorModel->associateAuthorToArticle($article_id, $author_id);

            header("Location: ../views/dashboard.php?success=1");
            exit();
        } else {
            header("Location: ../views/dashboard.php?error=1");
            exit();
        }
    }
    header("Location: ../views/dashboard.php?error=1");
    exit();
}


  // Obter um artigo pelo ID
  public function getById($id)
  {
    return $this->articleModel->getArticleById($id);
  }

  // Atualizar um artigo
  public function update($id, $title, $content)
  {
    if (!empty($id) && !empty($title) && !empty($content)) {
      if ($this->articleModel->updateArticles($id, $title, $content)) {
        return "Artigo atualizado com sucesso!";
      }
    }
    return "Erro ao atualizar o artigo.";
  }

  // Excluir um artigo
  public function delete($id)
  {
    if (!empty($id) && is_numeric($id)) {
      if ($this->articleModel->deleteArticle($id)) {
        echo json_encode(["success" => true, "message" => "Artigo excluído com sucesso!"]);
        exit();
      }
    }
    echo json_encode(["success" => false, "message" => "Erro ao excluir artigo."]);
    exit();
  }
}

// Processar requisição POST para criar artigo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
  $controller = new ArticleController();
  $controller->create($_POST['title'], $_POST['content']);
}

?>
