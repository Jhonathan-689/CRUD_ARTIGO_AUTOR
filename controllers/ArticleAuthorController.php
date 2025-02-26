<?php
require_once __DIR__ . '/../models/ArticleAuthorModel.php';

class ArticleAuthorController {
    private $articleAuthorModel;

    public function __construct() {
        $this->articleAuthorModel = new ArticleAuthorModel();
    }

    // Associar um autor a um artigo
    public function associateAuthorToArticle($article_id, $author_id) {
        if ($this->articleAuthorModel->associateAuthorToArticle($article_id, $author_id)) {
            return "Autor associado ao artigo com sucesso!";
        }
        return "Erro ao associar autor ao artigo.";
    }

    // Obter todos os autores de um artigo específico
    public function getAuthorsByArticle($article_id) {
        return $this->articleAuthorModel->getAuthorsByArticle($article_id);
    }

    // Obter todos os artigos de um autor específico
    public function getArticlesByAuthor($author_id) {
        return $this->articleAuthorModel->getArticlesByAuthor($author_id);
    }

    // Remover um autor de um artigo
    public function removeAuthorFromArticle($article_id, $author_id) {
        if ($this->articleAuthorModel->removeAuthorFromArticle($article_id, $author_id)) {
            return "Autor removido do artigo com sucesso!";
        }
        return "Erro ao remover autor do artigo.";
    }
}
