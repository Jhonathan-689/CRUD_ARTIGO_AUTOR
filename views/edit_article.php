<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
  header("Location: login.php");
  exit();
}

require_once __DIR__ . '/../models/ArticleModel.php';

$articleModel = new ArticleModel();
$article = $articleModel->getArticleById($_GET['id']);

if (!$article) {
  header("Location: my_publications.php?error=notfound");
  exit();
}
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
  <div class="container mt-5">
    <h2>Editar Artigo</h2>
    <form action="../controllers/ArticleController.php" method="POST">
      <input type="hidden" name="action" value="update"> <!-- Isso impede que `create()` seja chamado -->
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

      <button type="submit" class="btn btn-success">Salvar Alterações</button>
      <a href="my_publications.php" class="btn btn-secondary">Cancelar</a>
    </form>

  </div>
</body>

</html>