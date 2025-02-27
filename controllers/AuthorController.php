<?php
require_once __DIR__ . '/../models/AuthorModel.php';

class AuthorController
{
  private $authorModel;

  public function __construct(){
    $this->authorModel = new AuthorModel(); // Correção: não precisa instanciar o banco aqui
  }

  // Obter todos os autores
  public function getAll() // Correção: removido parâmetro desnecessário
  {
    return $this->authorModel->getAllAuthors();
  }

  // Obter um autor pelo ID
  public function getById($id)
  {
    return $this->authorModel->getAuthorById($id);
  }

  // Atualizar um autor
  public function update($id, $name, $email)
  {
    if ($this->authorModel->updateAuthor($id, $name, $email)) {
      return "Autor atualizado com sucesso!";
    }
    return "Erro ao atualizar o autor.";
  }

  // Excluir um autor
  public function delete($id)
  {
    if ($this->authorModel->deleteAuthor($id)) {
      return "Autor excluído com sucesso!";
    }
    return "Erro ao excluir autor.";
  }
}
?>
