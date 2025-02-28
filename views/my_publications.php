<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../models/ArticleModel.php';

$articleModel = new ArticleModel();
$author_id = $_SESSION['user_id'];
$articles = $articleModel->getArticlesByAuthor($author_id);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Publicações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script defer src="../public/js/form-validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Artigo Autores Lt Cloud</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../views/dashboard.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_publications.php">Minhas Publicações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../controllers/logoutController.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Minhas Publicações</h2>

        <?php if (!empty($articles)): ?>
            <ul class="list-group">
                <?php foreach ($articles as $article): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($article['title']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                        <small><strong>Publicado em:</strong> <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?></small>
                        <br>
                        <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm mt-2">Editar</a>
                        <button class="btn btn-danger btn-sm mt-2" onclick="deleteArticle(<?php echo $article['id']; ?>)">Excluir</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhuma publicação encontrada.</p>
        <?php endif; ?>
    </div>

    <script>
        function deleteArticle(articleId) {
            if (confirm("Tem certeza que deseja excluir este artigo?")) {
                window.location.href = "../controllers/ArticleController.php?action=delete&id=" + articleId;
            }
        }
    </script>
</body>
</html>
