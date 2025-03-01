<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../models/ArticleModel.php';
$articleModel = new ArticleModel();
$articles = $articleModel->getAllArticles();


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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script defer src="../public/js/form-validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Artigo Autores Lt Cloud</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Início</a>
                    </li>
                    <?php if ($_SESSION['role'] === 'author'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_publications.php">Minhas Publicações</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger"
                            href="../controllers/logoutController.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="greeting mb-4">
                    <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                </div>

                <?php if ($_SESSION['role'] === 'user'): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>Publicações de Autores</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($articles)): ?>
                                <ul class="list-group">
                                    <?php foreach ($articles as $article): ?>
                                        <li class="list-group-item">
                                            <h5><?php echo htmlspecialchars($article['title']); ?></h5>
                                            <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                                            <small>
                                                <strong>Autor:</strong> <?php echo htmlspecialchars($article['author_name']); ?> |
                                                <strong>Publicado em:</strong>
                                                <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?>
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Nenhuma publicação disponível no momento.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'author'): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>Escrever Artigo</h4>
                        </div>
                        <div class="card-body">
                            <form action="../controllers/ArticleController.php" method="POST">
                                <input type="hidden" name="action" value="create">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título do Artigo</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Conteúdo do Artigo</label>
                                    <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Publicar Artigo</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
