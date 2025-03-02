<?php
require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../models/AuthorModel.php';
require_once __DIR__ . '/../controllers/ArticleController.php';

$articleModel = new ArticleModel();
$authorModel = new AuthorModel();

$articles_per_page = 4;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$articleController = new ArticleController();
$paginationData = $articleController->getPaginatedArticles($page, $articles_per_page);

$articles = $paginationData['articles'];
$total_pages = $paginationData['total_pages'];
$current_page = $paginationData['current_page'];

$authors = $authorModel->getAllAuthors();

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

<body class="d-flex flex-column min-vh-100 overflow-hidden">
    <?php include __DIR__ . '/../public/navbar.php'; ?>
    <?php

    $articles_per_page = 4;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    $articleController = new ArticleController();
    $paginationData = $articleController->getPaginatedArticles($page, $articles_per_page);

    $articles = $paginationData['articles'];
    $total_pages = $paginationData['total_pages'];
    $current_page = $paginationData['current_page'];
    ?>

    <div class="container d-flex flex-column align-items-center justify-content-center flex-grow-1">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-8 col-md-10 col-12">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="greeting mb-4 text-center">
                    <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                </div>

                <?php if ($_SESSION['role'] === 'user'): ?>
                    <div class="card mt-3">
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
                                                <strong>Autores:</strong> <?php echo htmlspecialchars($article['author_names']); ?>
                                                |
                                                <strong>Publicado em:</strong>
                                                <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?>
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <?php if ($total_pages > 1): ?>
                                    <nav aria-label="Navegação de páginas" class="mt-3">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($current_page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Anterior</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($current_page < $total_pages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Próxima</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>

                            <?php else: ?>
                                <p class="text-center">Nenhuma publicação disponível no momento.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'author'): ?>
                    <div class="card mt-3 w-100">
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

                                <div class="mb-3">
                                    <label for="coauthors" class="form-label">Adicionar Coautores</label>
                                    <select class="form-control" id="coauthors" name="coauthors[]" multiple>
                                        <?php if (!empty($authors)): ?>
                                            <?php foreach ($authors as $author): ?>
                                                <?php if ($author['id'] != $_SESSION['user_id']): ?>
                                                    <option value="<?= htmlspecialchars($author['id']) ?>">
                                                        <?= htmlspecialchars($author['name']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>

                                    <?php if (empty($authors)): ?>
                                        <p class="text-danger mt-2 fw-medium">Nenhum coautor disponível.</p>
                                    <?php endif; ?>

                                    <small class="text-danger fw-medium">Segure Ctrl (ou Command no Mac) para selecionar ou
                                        remover múltiplos
                                        coautores.</small>
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