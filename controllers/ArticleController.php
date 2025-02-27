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
    if ($this->articleModel->createArticle($title, $content)) {
      return "Artigo criado com sucesso!";
    }
    return "Erro ao criar artigo.";
  }

  // Obter um artigo pelo ID
  public function getById($id)
  {
    return $this->articleModel->getArticleById($id);
  }

  // Atualizar um artigo
  public function update($id, $title, $content)
  {
    if ($this->articleModel->updateArticles($id, $title, $content)) {
      return "Artigo atualizado com sucesso!";
    }
    return "Erro ao atualizar o artigo.";
  }

  // Excluir um artigo
  public function delete($id)
  {
    if ($this->articleModel->deleteArticle($id)) {
      return "Artigo excluido com sucesso!";
    }
    return "Erro ao excluir artigo.";
  }
}

?>