<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
  header("Location: ../views/login.php");
  exit();
}

require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../models/ArticleAuthorModel.php';
require_once __DIR__ . '/../models/AuthorModel.php';

$articleModel = new ArticleModel();
$articleAuthorModel = new ArticleAuthorModel();
$authorModel = new AuthorModel();

$article = $articleModel->getArticleById($_GET['id'] ?? null);

if (!$article) {
  $_SESSION['message'] = "Artigo não encontrado!";
  $_SESSION['message_type'] = "danger";
  header("Location: ../views/my_publications.php");
  exit();
}

$coauthors = $articleAuthorModel->getAuthorsByArticle($article['id']);
$allAuthors = $authorModel->getAllAuthors();

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? 'info';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Artigo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
  <?php include __DIR__ . '/../public/navbar.php'; ?>
  <div class="container mt-5">
    <h2>Editar Artigo</h2>

    <?php if (!empty($message)): ?>
      <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <form action="../controllers/ArticleController.php" method="POST">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id']); ?>">

      <div class="mb-3">
        <label for="title" class="form-label">Título do Artigo</label>
        <input type="text" class="form-control" id="title" name="title"
          value="<?php echo htmlspecialchars($article['title']); ?>" required>
      </div>

      <div class="mb-3">
        <label for="content" class="form-label">Conteúdo do Artigo</label>
        <textarea class="form-control" id="content" name="content" rows="6"
          required><?php echo htmlspecialchars($article['content']); ?></textarea>
      </div>

      <div class="mb-3">
        <label for="coauthors" class="form-label">Coautores</label>
        <select class="form-control" id="coauthors" name="coauthors[]" multiple>
          <?php
          require_once __DIR__ . '/../models/ArticleAuthorModel.php';
          require_once __DIR__ . '/../models/AuthorModel.php';

          $articleAuthorModel = new ArticleAuthorModel();
          $authorModel = new AuthorModel();

          $coauthors = $articleAuthorModel->getAuthorsByArticle($article['id']);
          $allAuthors = $authorModel->getAllAuthors();

          foreach ($allAuthors as $author) {
            if ($author['id'] !== $_SESSION['user_id']) { // Evita listar o autor principal como coautor
              $selected = in_array($author['id'], array_column($coauthors, 'id')) ? 'selected' : '';
              echo "<option value='{$author['id']}' $selected>{$author['name']}</option>";
            }
          }
          ?>
        </select>
        <small class="text-danger fw-medium">Segure Ctrl (ou Command no Mac) para selecionar ou remover múltiplos
          coautores..</small>
      </div>

      <button type="submit" class="btn btn-success">Salvar Alterações</button>
      <a href="my_publications.php" class="btn btn-secondary">Cancelar</a>
    </form>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>