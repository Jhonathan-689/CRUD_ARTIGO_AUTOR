<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../models/ArticleModel.php';

$articleModel = new ArticleModel();
$author_id = $_SESSION['user_id'];

$articles_per_page = 2;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $articles_per_page;

// Obtem total de artigos criados e como coautor
$total_my_articles = $articleModel->countUserArticles($author_id, false);
$total_coauthored_articles = $articleModel->countUserArticles($author_id, true);

$total_pages_my = ceil($total_my_articles / $articles_per_page);
$total_pages_coauthored = ceil($total_coauthored_articles / $articles_per_page);

$myArticles = $articleModel->getUserArticlesPaginated($author_id, $articles_per_page, $offset, false);
$coauthoredArticles = $articleModel->getUserArticlesPaginated($author_id, $articles_per_page, $offset, true);

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
    <title>Minhas Publicações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script defer src="../public/js/form-validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include __DIR__ . '/../public/navbar.php'; ?>

    <main class="container mt-5 content">
        <h2 class="mb-4">Minhas Publicações</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h4 class="mt-4">Artigos Criados</h4>
        <?php if (!empty($myArticles)): ?>
            <div class="list-group">
                <?php foreach ($myArticles as $article): ?>
                    <div class="list-group-item">
                        <h5><?php echo htmlspecialchars($article['title']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                        <small><strong>Publicado em:</strong> <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?></small>
                        <br>
                        <small><strong>Última edição por:</strong>
                            <?php echo ($article['last_edited_by'] == $_SESSION['user_id']) ? "Você" : htmlspecialchars($article['last_edited_name']); ?>
                        </small>
                        <br>
                        <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <button class="btn btn-danger btn-sm" onclick="deleteArticle(<?php echo $article['id']; ?>)">Excluir</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <nav aria-label="Navegação de páginas" class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Anterior</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages_my; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages_my): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Próxima</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php else: ?>
            <p>Nenhuma publicação criada.</p>
        <?php endif; ?>

        <h4 class="mt-4">Artigos como Coautor</h4>
        <?php if (!empty($coauthoredArticles)): ?>
            <div class="list-group">
                <?php foreach ($coauthoredArticles as $article): ?>
                    <div class="list-group-item">
                        <h5><?php echo htmlspecialchars($article['title']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                        <small><strong>Publicado em:</strong> <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?></small>
                        <br>
                        <small><strong>Última edição por:</strong> <?php echo htmlspecialchars($article['last_edited_name']); ?></small>
                        <br>
                        <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <nav aria-label="Navegação de páginas" class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Anterior</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages_coauthored; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages_coauthored): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Próxima</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php else: ?>
            <p>Você ainda não participa como coautor de nenhum artigo.</p>
        <?php endif; ?>
    </main>
    <script>
        function deleteArticle(articleId) {
            if (confirm("Tem certeza que deseja excluir este artigo?")) {
                window.location.href = "../controllers/ArticleController.php?action=delete&id=" + articleId;
            }
        }
    </script>
</body>

</html>
