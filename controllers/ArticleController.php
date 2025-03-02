<?php
session_start();
require_once __DIR__ . '/../models/ArticleModel.php';

class ArticleController
{
    private $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    public function create($title, $content, $coauthors = [])
    {
        $author_id = $_SESSION['user_id'] ?? null;

        if (!empty($title) && !empty($content) && !empty($author_id)) {

            $coauthors = isset($_POST['coauthors']) ? $_POST['coauthors'] : [];


            $article_id = $this->articleModel->createArticle($title, $content, $author_id, $coauthors);

            if ($article_id) {
                $_SESSION['message'] = "Artigo publicado com sucesso!";
                $_SESSION['message_type'] = "success";
                header("Location: ../views/dashboard.php");
                exit();
            }
        }

        $_SESSION['message'] = "Erro ao publicar o artigo.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/dashboard.php");
        exit();
    }

    public function getById($id)
    {
        return $this->articleModel->getArticleById($id);
    }

    public function update($id, $title, $content, $coauthors = [])
    {
        $main_author_id = $_SESSION['user_id'];

        if (!empty($id) && !empty($title) && !empty($content)) {
            if ($this->articleModel->updateArticle($id, $title, $content, $coauthors, $main_author_id)) {
                $_SESSION['message'] = "Artigo atualizado com sucesso!";
                $_SESSION['message_type'] = "success";
                header("Location: ../views/my_publications.php");
                exit();
            }
        }

        $_SESSION['message'] = "Erro ao atualizar o artigo.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/my_publications.php");
        exit();
    }

    public function getPaginatedArticles($page, $articles_per_page)
    {
        $total_articles = $this->articleModel->countAllArticles();
        $total_pages = ceil($total_articles / $articles_per_page);

        if ($total_articles > 0 && $page > $total_pages) {
            $page = max(1, $total_pages);
        }

        $offset = ($page - 1) * $articles_per_page;

        $articles = $this->articleModel->getPaginatedArticles($articles_per_page, $offset);

        if (empty($articles) && $page > 1) {
            return $this->getPaginatedArticles($page - 1, $articles_per_page);
        }

        return [
            'articles' => $articles,
            'total_articles' => $total_articles,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }

    public function delete($id)
    {
        if (!empty($id) && is_numeric($id)) {
            require_once __DIR__ . '/../models/ArticleAuthorModel.php';
            $articleAuthorModel = new ArticleAuthorModel();

            require_once __DIR__ . '/../models/ArticleModel.php';
            $articleModel = new ArticleModel();
            $article = $articleModel->getArticleById($id);

            if ($article) {
                $author_id = $_SESSION['user_id'];

                $articleAuthorModel->removeAuthorFromArticle($id, $author_id);

                if ($this->articleModel->deleteArticle($id)) {
                    $_SESSION['message'] = "Artigo excluído com sucesso.";
                    $_SESSION['message_type'] = "success";
                    header("Location: ../views/my_publications.php");
                    exit();
                }
            }
        }

        $_SESSION['message'] = "Erro ao excluir o artigo.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../views/my_publications.php");
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $controller = new ArticleController();
    $controller->delete($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ArticleController();

    if (!empty($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['content'])) {
                    $coauthors = isset($_POST['coauthors']) ? $_POST['coauthors'] : []; // ⚠️ CORREÇÃO FEITA AQUI!
                    $controller->update($_POST['id'], $_POST['title'], $_POST['content'], $coauthors);
                }
                break;

            case 'create':
                if (!empty($_POST['title']) && !empty($_POST['content'])) {
                    $coauthors = isset($_POST['coauthors']) ? $_POST['coauthors'] : []; // Garante que coautores sejam enviados
                    $controller->create($_POST['title'], $_POST['content'], $coauthors);
                }
                break;
        }
    }


    $_SESSION['message'] = "Erro ao processar a requisição.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../views/my_publications.php");
    exit();

}
