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
              header("Location: ../views/my_publications.php?success=1");
              exit();
          }
      }
      header("Location: ../views/my_publications.php?error=1");
      exit();
  }

  // Excluir um artigo
  public function delete($id)
{
    if (!empty($id) && is_numeric($id)) {
        require_once __DIR__ . '/../models/ArticleAuthorModel.php';
        $articleAuthorModel = new ArticleAuthorModel();

        // Obter o ID do autor associado ao artigo antes de excluí-lo
        require_once __DIR__ . '/../models/ArticleModel.php';
        $articleModel = new ArticleModel();
        $article = $articleModel->getArticleById($id);

        if ($article) {
            $author_id = $_SESSION['user_id']; // O autor logado é o dono do artigo

            // Primeiro, remover a associação do artigo com o autor
            $articleAuthorModel->removeAuthorFromArticle($id, $author_id);

            // Depois, excluir o artigo
            if ($this->articleModel->deleteArticle($id)) {
                header("Location: ../views/my_publications.php?success=1");
                exit();
            }
        }
    }
    header("Location: ../views/my_publications.php?error=1");
    exit();
}

}
// Processar requisição POST para criar artigo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
  $controller = new ArticleController();
  $controller->create($_POST['title'], $_POST['content']);
}

// Processar requisição GET para excluir artigo
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
  $controller = new ArticleController();
  $controller->delete($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $controller = new ArticleController();
  
  // Verificar se a ação foi enviada corretamente
  if (!empty($_POST['action']) && $_POST['action'] === 'update') {
      if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['content'])) {
          $controller->update($_POST['id'], $_POST['title'], $_POST['content']);
          exit();
      }
  } elseif (empty($_POST['action']) && !empty($_POST['title']) && !empty($_POST['content'])) {
      $controller->create($_POST['title'], $_POST['content']);
      exit();
  }
}

?>
