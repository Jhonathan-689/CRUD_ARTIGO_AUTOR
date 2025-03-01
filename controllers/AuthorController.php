<?php
require_once __DIR__ . '/../models/AuthorModel.php';

class AuthorController
{
  private $authorModel;

  public function __construct(){
    $this->authorModel = new AuthorModel();
  }

  public function getAll() 
  {
    return $this->authorModel->getAllAuthors();
  }

  public function getById($id)
  {
    return $this->authorModel->getAuthorById($id);
  }

  public function update($id, $name, $email)
  {
    if ($this->authorModel->updateAuthor($id, $name, $email)) {
      return "Autor atualizado com sucesso!";
    }
    return "Erro ao atualizar o autor.";
  }

  public function delete($id)
  {
    if ($this->authorModel->deleteAuthor($id)) {
      return "Autor excluído com sucesso!";
    }
    return "Erro ao excluir autor.";
  }
}
?>
